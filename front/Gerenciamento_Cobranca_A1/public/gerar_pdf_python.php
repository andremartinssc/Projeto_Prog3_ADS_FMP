<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fatura_id = $_POST['fatura_id'];
    $nome_cliente = $_POST['nome_cliente'];
    $valor_fatura = $_POST['valor_fatura'];
    $data_vencimento = $_POST['data_vencimento'];

    // Caminho correto para o script Python
    $python_script = 'C:\\RPA\\Projeto_Prog3_ADS_FMP\\gera_faturas_python.py';

    // Caminho para o interpretador Python (ajuste se necessário)
    $python_executable = 'C:\\Python312\\python.exe'; // Verifique o caminho correto do seu Python

    // Nome do arquivo PDF de saída (será criado pelo script Python)
    $output_pdf = 'fatura_' . $fatura_id . '.pdf';

    // Comando para executar o script Python, passando os dados como argumentos
    $command = sprintf(
        '%s "%s" --id "%s" --nome "%s" --valor "%s" --vencimento "%s" --output "%s"',
        escapeshellarg($python_executable),
        escapeshellarg($python_script),
        escapeshellarg($fatura_id),
        escapeshellarg($nome_cliente),
        escapeshellarg($valor_fatura),
        escapeshellarg($data_vencimento),
        escapeshellarg($output_pdf)
    );

    // Executar o comando
    $output = shell_exec($command);
    echo "<pre>";
    echo "Comando executado: " . htmlspecialchars($command) . "\n";
    echo "Saída do script Python:\n" . htmlspecialchars($output);
    echo "</pre>";

    // Forçar o download do PDF gerado
    if (file_exists($output_pdf)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($output_pdf) . '"');
        header('Content-Length: ' . filesize($output_pdf));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        readfile($output_pdf);

        // Opcional: Excluir o arquivo PDF após o download
        unlink($output_pdf);
        exit;
    } else {
        echo "Erro: Arquivo PDF não encontrado após a execução do script Python.";
    }

} else {
    echo "Acesso inválido.";
}

?>