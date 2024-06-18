<?php

class Formatter {
    
    public static function formatDataAbertura($dataAbertura) {
        return date('d/m/Y', strtotime($dataAbertura));
    }

    public static function formatCPF($cpf) {
        return substr($cpf, 0, 3) . '.' . 
               substr($cpf, 3, 3) . '.' . 
               substr($cpf, 6, 3) . '-' . 
               substr($cpf, 9, 2);
    }

}

?>