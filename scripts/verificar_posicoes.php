<?php
// Script simples para verificar posições
$line = file('temp/tb_procedimento.txt')[0];
$line = rtrim($line, "\r\n");

echo "Tamanho da linha: " . strlen($line) . "\n";
echo "Últimos 30 caracteres: " . substr($line, -30) . "\n";
echo "Posição 321-326 (DT_COMPETENCIA): [" . substr($line, 320, 6) . "]\n";
echo "Posição 315-320 (CO_RUBRICA): [" . substr($line, 314, 6) . "]\n";
echo "Posição 313-314 (CO_FINANCIAMENTO): [" . substr($line, 312, 2) . "]\n";
echo "Posição 303-312 (VL_SP): [" . substr($line, 302, 10) . "]\n";
echo "Posição 293-302 (VL_SA): [" . substr($line, 292, 10) . "]\n";
echo "Posição 283-292 (VL_SH): [" . substr($line, 282, 10) . "]\n";

