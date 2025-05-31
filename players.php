<?php

// Inclua a SampQueryAPI (supondo que você já tenha a biblioteca no projeto)
require "SampQueryAPI.php";  // Altere o caminho conforme a sua estrutura

// Configurações do servidor SA:MP
$server_ip = '181.215.254.209'; // Substitua pelo IP do servidor
$server_port = 7777;      // Substitua pela porta do servidor

// Inicializa os arrays
$Config['mServersOnline'] = [];
$Config['mServersPing'] = [];
$Config['mServersDoubling'] = [];
$Config['mServersIsNew'] = [];

// Conecta ao servidor SA:MP usando a SampQueryAPI e busca dados
try {
    // Cria uma instância da API
    $query = new SampQueryAPI($server_ip, $server_port);

    // Tenta conectar ao servidor
    if ($query->isOnline()) {
        // Obtém informações do servidor
        $info = $query->getInfo();
        
        if ($info !== false) {
            // Preenche os arrays com as informações obtidas
            $Config['mServersOnline'][] = $info['players'];
            $Config['mServersPing'][] = $info['ping'] ?? 128;
            $Config['mServersDoubling'][] = 1;  // Valor exemplo
            $Config['mServersIsNew'][] = 1;     // Valor exemplo

            // Formatar e imprimir como JSON no formato desejado
            $json_output = [
                "servers" => [
                    [
                        "players1" => $Config['mServersOnline'][0],
                        "ping" => $Config['mServersPing'][0],
                        "doubling" => $Config['mServersDoubling'][0],
                        "new" => $Config['mServersIsNew'][0]
                    ]
                ]
            ];

            // Exibe o JSON formatado
            echo json_encode($json_output);
        } else {
            echo "Erro: Não foi possível obter as informações do servidor.";
        }
    } else {
        echo "Erro: Não foi possível conectar ao servidor SA:MP.";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
