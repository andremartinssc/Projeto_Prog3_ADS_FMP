<?php
require_once '../includes/conexao.php';

$filename = "faturas_com_dados_cliente.csv";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: no-store, no-cache");

$output = fopen("php://output", "w");
fputcsv($output, array(
    'Fatura_ID', 'Cliente_Nome', 'Cliente_Email', 'Cliente_Telefone', 
    'Fatura_Valor', 'Fatura_Data_Vencimento', 'Fatura_Status'
)); // Cabeçalho completo

$sql = "SELECT 
        f.id as fatura_id,
        c.nome as cliente_nome,
        c.email as cliente_email,
        c.telefone as cliente_telefone,
        f.valor as fatura_valor,
        f.data_vencimento as fatura_data_vencimento,
        f.status as fatura_status
        FROM faturas f
        JOIN clientes c ON f.cliente_id = c.id
        ORDER BY f.data_vencimento";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $row['fatura_id'],
            $row['cliente_nome'],
            $row['cliente_email'],
            $row['cliente_telefone'],
            $row['fatura_valor'],
            $row['fatura_data_vencimento'],
            $row['fatura_status']
        ));
    }
}

fclose($output);
exit;
?>