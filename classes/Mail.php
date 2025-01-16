<?php

namespace WordpressTema;

class Mail
{
    public static function send($to, $subject, $template, $data = [], $attachments = [])
    {
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>',
        ];

        $message = self::get_template($template, $data);

        $status = wp_mail($to, $subject, $message, $headers, $attachments);

        return $status;
    }

    private static function  get_template($template, $data)
    {
        ob_start();
        get_template_part('templates/mail/' . $template, null, $data);
        return ob_get_clean();
    }
}
