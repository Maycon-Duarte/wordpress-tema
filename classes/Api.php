<?php

namespace WordpressTema;

use WP_Query;
use WP_REST_Request;
use WP_REST_Response;

use function register_rest_route;
use function get_posts;
use function is_email;
use function is_numeric;
use function get_the_ID;
use function get_the_title;
use function get_post_field;
use function get_the_date;
use function human_time_diff;
use function get_the_time;
use function current_time;
use function get_the_category;
use function get_the_post_thumbnail_url;
use function get_field;
use function get_the_excerpt;
use function wp_kses_post;
use function apply_filters;
use function get_the_content;
use function wp_reset_postdata;
use function setup_postdata;
use function get_fields;
use function get_the_terms;
use function is_wp_error;
use function get_post_time;

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


        // Rota para obter um projeto pelo slug (post_type = projeto)
        register_rest_route('api/v1', 'projeto/(?P<slug>[-a-zA-Z0-9_]+)', [
            'methods' => ['GET'],
            'callback' => [self::class, 'get_projeto_by_slug'],
            'permission_callback' => '__return_true',
        ]);

        // Rota para listar projetos (com filtro por categoria)
        register_rest_route('api/v1', 'projetos', [
            'methods' => ['GET'],
            'callback' => [self::class, 'get_projetos'],
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

    public static function get_news(WP_REST_Request $request)
    {
        $errors = self::request_validate($request, [
            'ppp' => ['numeric'],
            'page' => ['numeric'],
        ]);

        if (!empty($errors)) {
            return new WP_REST_Response([
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

        $query = new WP_Query($args);

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
                    'featuredImageModal' => get_field('thumb_modal', get_the_ID()) ?: 'https://api-ifdo.lojahomologacao.com.br/wp-content/uploads/2025/10/Link-%E2%86%92-Studio-Selects-brand-identity-for-perfumery-Serviette-is-steeped-in-sophistication.jpg',
                    'excerpt' => get_the_excerpt(),
                    'content' => wp_kses_post(apply_filters('the_content', get_the_content())),
                    'ratio' => get_field('tamanho_do_card') ?: '1/1',
                ];
            }
        }

        wp_reset_postdata();

        return new WP_REST_Response([
            'status' => true,
            'data' => $news,
            'max_pages' => $query->max_num_pages,
        ], 200);
    }

    /**
     * Retorna um projeto (post_type = projeto) pelo slug incluindo campos ACF
     * GET /wp-json/api/v1/projeto/{slug}
     */
    public static function get_projeto_by_slug(WP_REST_Request $request)
    {
        $slug = $request->get_param('slug');

        if (empty($slug)) {
            return new WP_REST_Response([
                'status' => false,
                'errors' => ['slug' => 'O slug é obrigatório'],
            ], 200);
        }

        $posts = get_posts([
            'name' => $slug,
            'post_type' => 'projeto',
            'post_status' => 'publish',
            'numberposts' => 1,
        ]);

        if (empty($posts)) {
            return new WP_REST_Response([
                'status' => false,
                'errors' => ['not_found' => 'Projeto não encontrado'],
            ], 200);
        }
        $post = $posts[0];
        setup_postdata($post);

        // Pegar todos os campos ACF se disponível
        $acf = function_exists('get_fields') ? get_fields($post->ID) : [];

        // tags definidas via ACF (repeater 'tags' -> subfield 'tag')
        $tags = [];
        if (!empty($acf) && !empty($acf['tags']) && is_array($acf['tags'])) {
            foreach ($acf['tags'] as $row) {
                if (!empty($row['tag'])) {
                    $tags[] = $row['tag'];
                }
            }
        }

        $item = [
            'id' => $post->ID,
            'title' => get_the_title($post->ID),
            'slug' => $slug,
            'date' => get_the_date('j M Y', $post->ID),
            'dateRelative' => human_time_diff(get_post_time('U', true, $post), current_time('timestamp')) . ' atrás',
            'tags' => $tags,
            'featuredImage' => get_the_post_thumbnail_url($post->ID, 'large'),
            'excerpt' => get_the_excerpt($post->ID),
            'content' => wp_kses_post(apply_filters('the_content', $post->post_content)),
            'ratio' => get_field('tamanho_do_card', $post->ID) ?: '1/1',
            'acf' => $acf,
        ];

        wp_reset_postdata();

        return new WP_REST_Response([
            'status' => true,
            'data' => $item,
        ], 200);
    }

    /**
     * Lista projetos com filtro opcional por categoria
     * GET /wp-json/api/v1/projetos?categoria=slug-da-categoria&ppp=10&page=1
     */
    public static function get_projetos(WP_REST_Request $request)
    {
        $errors = self::request_validate($request, [
            'ppp' => ['numeric'],
            'page' => ['numeric'],
            'categoria' => ['string'],
        ]);

        if (!empty($errors)) {
            return new WP_REST_Response([
                'status' => false,
                'errors' => $errors,
            ], 200);
        }

        $data = $request->get_params();

        $ppp = !empty($data['ppp']) ? intval($data['ppp']) : 10;
        $page = !empty($data['page']) ? intval($data['page']) : 1;
        $categoria = !empty($data['categoria']) ? sanitize_text_field($data['categoria']) : '';

        $args = [
            'post_type' => 'projeto',
            'posts_per_page' => $ppp,
            'paged' => $page,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        // Filtrar por categoria se fornecida
        if (!empty($categoria)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'categoria-de-projeto',
                    'field'    => 'slug',
                    'terms'    => $categoria,
                ],
            ];
        }

        $query = new WP_Query($args);

        $projetos = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                // Pegar categorias do projeto
                $categorias = [];
                $project_terms = get_the_terms(get_the_ID(), 'categoria-de-projeto');
                if (!empty($project_terms) && !is_wp_error($project_terms)) {
                    foreach ($project_terms as $term) {
                        $categorias[] = [
                            'id' => $term->term_id,
                            'name' => $term->name,
                            'slug' => $term->slug,
                        ];
                    }
                }

                // Pegar campos ACF se disponível
                $acf = function_exists('get_fields') ? get_fields(get_the_ID()) : [];

                // tags definidas via ACF (repeater 'tags' -> subfield 'tag')
                $tags = [];
                if (!empty($acf) && !empty($acf['tags']) && is_array($acf['tags'])) {
                    foreach ($acf['tags'] as $row) {
                        if (!empty($row['tag'])) {
                            $tags[] = $row['tag'];
                        }
                    }
                }

                $projetos[] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'slug' => get_post_field('post_name', get_the_ID()),
                    'date' => get_the_date('j M Y'),
                    'dateRelative' => human_time_diff(get_the_time('U'), current_time('timestamp')) . ' atrás',
                    'categorias' => $categorias,
                    'tags' => $tags,
                    'featuredImage' => get_the_post_thumbnail_url(get_the_ID(), 'large'),
                    'excerpt' => get_the_excerpt(),
                    'ratio' => get_field('tamanho_do_card') ?: '1/1',
                ];
            }
        }

        wp_reset_postdata();

        return new WP_REST_Response([
            'status' => true,
            'data' => $projetos,
            'max_pages' => $query->max_num_pages,
            'total' => $query->found_posts,
        ], 200);
    }

    /**
     * Valida se um CPF é válido
     */
    public static function is_cpf($cpf)
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula e verifica o primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;

        if ($cpf[9] != $digit1) {
            return false;
        }

        // Calcula e verifica o segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;

        return $cpf[10] == $digit2;
    }
}
