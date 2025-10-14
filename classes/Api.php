<?php

namespace WordpressTema;

class Api
{
    use Hooker;

    protected static $actions = [
        'rest_api_init' => 'rest_init',
    ];

    public static function init()
    {
        self::register_hooks();
    }

    public static function rest_init()
    {
        register_rest_route('api/v1', 'news', [
            'methods' => ['GET'],
            'callback' => [self::class, 'get_news'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function request_validate($request, $rules)
    {
        $data = $request->get_params();
        $errors = [];

        foreach ($rules as $field => $validations) {
            foreach ($validations as $rule => $value) {
                // Verifica se a regra não tem valor (é um simples "flag")
                if (is_int($rule)) {
                    // Se for um índice numérico, defina a regra e o valor como true
                    $rule = $value;
                    $value = true;
                }

                // Processamento das regras
                switch ($rule) {
                    case 'required':
                        if (empty($data[$field])) {
                            $errors[$field] = "Este campo é obrigatório";
                        }
                        break;

                    case 'required_if':
                        if (empty($data[$value[0]])) {
                            break; // Não precisa validar se a condição inicial não foi atendida
                        }

                        if ($data[$value[0]] === $value[1] && empty($data[$field])) {
                            $errors[$field] = "Este campo é obrigatório";
                        }
                        break;

                    case 'confirmed':
                        if ($data[$field] !== $data[$field . '_confirmation']) {
                            $errors[$field] = "A confirmação de $field não corresponde";
                        }
                        break;

                    case 'string':
                        if (!is_string($data[$field])) {
                            $errors[$field] = "$field deve ser uma string";
                        }
                        break;

                    case 'only':
                        if (!in_array($data[$field], $value)) {
                            $errors[$field] = "$field deve ser um dos seguintes: " . implode(', ', $value);
                        }
                        break;

                    case 'cpf':
                        if (empty($data[$field])) break;
                        if (!self::is_cpf($data[$field])) {
                            $errors[$field] = "$field é inválido";
                        }
                        break;

                    case 'numeric':
                        if (!is_numeric($data[$field])) {
                            $errors[$field] = "$field deve ser um número";
                        }
                        break;

                    case 'email':
                        if (!is_email($data[$field])) {
                            $errors[$field] = "$field deve ser um e-mail válido";
                        }
                        break;

                    case 'image':
                        if (!empty($_FILES[$field]) && !in_array($_FILES[$field]['type'], ['image/jpeg', 'image/png'])) {
                            $errors[$field] = "$field deve ser uma imagem válida (jpg, png)";
                        }
                        break;

                    case 'max_size':
                        if (!empty($_FILES[$field]) && $_FILES[$field]['size'] > $value * 1024) {
                            $errors[$field] = "$field excede o tamanho máximo de " . ($value / 1024) . " MB";
                        }
                        break;

                    case 'allowed_types':
                        if (!empty($_FILES[$field]) && !in_array($_FILES[$field]['type'], $value)) {
                            $errors[$field] = "$field tem um tipo de arquivo inválido";
                        }
                        break;

                    case 'exists':
                        $value = explode(':', $value);
                        $table = $value[0];
                        $column = $value[1];

                        if ($column == 'post_title') {
                            $args = ['post_type' => $table, 'title' => $data[$field]];
                            $result = get_posts($args);
                        } else {
                            $result = get_posts([
                                'post_type' => $table,
                                'meta_query' => [
                                    [
                                        'key' => $column,
                                        'value' => $data[$field],
                                    ],
                                ],
                            ]);
                        }

                        if (empty($result)) {
                            $errors[$field] = "$field não existe";
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    public static function get_news(\WP_REST_Request $request)
    {
        $errors = self::request_validate($request, [
            'ppp' => ['numeric'],
            'page' => ['numeric'],
        ]);

        if (!empty($errors)) {
            return new \WP_REST_Response([
                'status' => false,
                'errors' => $errors,
            ], 200);
        }

        $data = $request->get_params();

        $ppp = !empty($data['ppp']) ? intval($data['ppp']) : 10;
        $page = !empty($data['page']) ? intval($data['page']) : 1;

        $args = [
            'post_type' => 'news',
            'posts_per_page' => $ppp,
            'paged' => $page,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        $query = new \WP_Query($args);

        $news = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $news[] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'slug' => get_post_field('post_name', get_the_ID()),
                    'date' => get_the_date('j M Y'),
                    'dateRelative' => human_time_diff(get_the_time('U'), current_time('timestamp')) . ' atrás',
                    'category' => get_the_category()[0]->name ?? 'Uncategorized',
                    'featuredImage' => get_the_post_thumbnail_url(get_the_ID(), 'large'),
                    'excerpt' => get_the_excerpt(),
                    'content' => apply_filters('the_content', get_the_content()),
                    'ratio' => get_field('tamanho_do_card') ?: '1/1',
                ];
            }
        }

        wp_reset_postdata();

        return new \WP_REST_Response([
            'status' => true,
            'data' => $news,
            'max_pages' => $query->max_num_pages,
        ], 200);
    }
}
