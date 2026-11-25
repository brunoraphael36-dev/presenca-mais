<?php

namespace app\components\helpers;

class StringHelper
{
    public static function removeAcentos($input)
    {
        // Se $input for uma instância de modelo, assume que a propriedade relevante é 'nmTitulo'
        if (is_object($input) && property_exists($input, 'nmTitulo')) {
            $string = $input->nmTitulo;
        } else {
            // Se $input for uma string, usa diretamente
            $string = $input;
        }

        // Define a lista de caracteres especiais a serem removidos ou substituídos
        $caracteres_especiais = array(
            '@'=>'',
            '/'=>'',
            '('=>'',
            ')'=>'',
            '{'=>'',
            '}'=>'',
            '['=>'',
            ']'=>''
        );

        // Remove ou substitui os caracteres especiais
        $string = strtr($string, $caracteres_especiais);
        // Converte para minúscula
        $string = strtolower($string);
        // Remove a pontuação especificada
        $string = preg_replace("/[,:;.!?]+/", "", $string);
        // Comprime múltiplas ocorrências de espaços
        $string = preg_replace("/[\s]+/", " ", $string);
        // Comprime múltiplas ocorrências de underscores
        $string = preg_replace("/[_]+/", "_", $string);
        // Comprime múltiplas ocorrências de hífens
        $string = preg_replace("/[-]+/", "-", $string);
        // Comprime múltiplas ocorrências de barras
        $string = preg_replace("/[\/]+/", "-", $string);
        // Comprime múltiplas ocorrências de barras invertidas
        $string = preg_replace("/[\\\]+/", "-", $string);
        // Remove espaços antes e após hífens
        $string = preg_replace("/[[\s]+]?-[[\s]+]?/", "-", $string);
        // Converte espaços em hífens
        $string = preg_replace("/[\s]/", "-", $string);
        // Converte underscores em hífens
        $string = preg_replace("/[_]/", "-", $string);
        // Converte barras em hífens
        $string = preg_replace("/[\/]/", "-", $string);
        // Converte barras invertidas em hífens
        $string = preg_replace("/[\\\]/", "-", $string);

        $url = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"),explode(" ","a a e e i i o o u u n n c c"),$string);
        return $url;
    }
}
