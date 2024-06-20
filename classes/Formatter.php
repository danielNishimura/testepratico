<?php

class Formatter {

    public static function validarCPF($cpf) {
        return preg_match('/^[0-9]{11}$/', $cpf);
    }
    
    public static function formatDataAbertura($dataAbertura) {
        return date('d/m/Y', strtotime($dataAbertura));
    }

    public static function formatCPF($cpf) {
        // Lógica de formatação do CPF, se necessário
        return preg_replace("/^(\d{3})(\d{3})(\d{3})(\d{2})$/", "$1.$2.$3-$4", $cpf);
    }

    public static function displayAlert($message, $type = 'sucess') {
        if (!empty($message)) {
            echo '<div class="alert alert-' . htmlspecialchars($type) . '" role="alert">';
            echo htmlspecialchars($message);
            echo '</div>';
        }
    }

}

?>