<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

if (!function_exists('onlyNumber')) {
    /**
     * Função responsável por retornar apenas os números de uma string
     *
     * @param string $value Texto a ser formatado
     *
     * @access public
     * @return mixed
     */
    function onlyNumber($value)
    {
        $value = preg_replace("/[^\d]/", "", (string)$value);
        return !empty($value) ? $value : null;
    }
}

if (!function_exists('formatCpf')) {
    /**
     * Função responsável por formatar um número como um CPF (Ex.: 000.000.000-00)
     *
     * @param string $value Número do CPF
     *
     * @access public
     * @return string
     */
    function formatCpf($value, bool $onlyNumber = true): string
    {
        if ($onlyNumber) {
            $value = onlyNumber($value);
        }

        $value = substr($value, 0, 11);

        if (strlen($value) == 11) {
            return substr($value, 0, 3) . '.' . substr($value, 3, 3) . '.' . substr($value, 6, 3) . '-' . substr($value, 9, 2);
        }

        return $value;
    }
}

if (!function_exists('formatCnpj')) {
    /**
     * Função responsável por formatar um número como um CNPJ (Ex.: 00.000.000/0001-00)
     *
     * @param string $value Número do CNPJ
     *
     * @access public
     * @return string
     */
    function formatCnpj($value, bool $onlyNumber = true): string
    {
        if ($onlyNumber) {
            $value = onlyNumber($value);
        }

        $value = substr($value, 0, 14);

        if (strlen($value) == 14) {
            return substr($value, 0, 2) . '.' . substr($value, 2, 3) . '.' . substr($value, 5, 3) . '/' . substr($value, 8, 4) . '-' . substr($value, 12, 2);
        }

        return $value;
    }
}

if (!function_exists('formatCpfCnpj')) {
    /**
     * Função responsável por formatar um número como um CPF ou CNPJ dependendo do seu tamanho (Ex.: 000.000.000-00 / 00.000.000/0001-00)
     *
     * @param string $value Número do CPF ou CNPJ
     *
     * @access public
     * @return string
     */
    function formatCpfCnpj($value, bool $onlyNumber = true): string
    {
        if ($onlyNumber) {
            $value = onlyNumber($value);
        }

        if (strlen($value) <= 11) {
            return formatCpf($value, $onlyNumber);
        } else {
            return formatCnpj($value, $onlyNumber);
        }
    }
}

if (!function_exists('formatPhone')) {
    /**
     * Função responsável por formatar um número como um telefone (Ex.: (00) 00000-0000)
     *
     * @param string $value Número do telefone
     *
     * @access public
     * @return string
     */
    function formatPhone($value): string
    {
        $value = onlyNumber($value);
        $value = substr($value, 0, 11);

        if (strlen($value) >= 10) {
            if (strlen($value) == 11) {
                return '(' . substr($value, 0, 2) . ') ' . substr($value, 2, 5) . '-' . substr($value, 7, 4);
            } else {
                return '(' . substr($value, 0, 2) . ') ' . substr($value, 2, 4) . '-' . substr($value, 6, 4);
            }
        }

        return $value;
    }
}

if (!function_exists('formatPostalCode')) {
    /**
     * Função responsável por formatar um número como um CEP (Ex.: 00000-000)
     *
     * @param string $value Número do CPF ou CNPJ
     *
     * @access public
     * @return string
     */
    function formatPostalCode($value): string
    {
        $value = onlyNumber($value);
        $value = substr($value, 0, 8);

        if (strlen($value) == 8) {
            return substr($value, 0, 5) . '-' . substr($value, 5, 3);
        }

        return $value;
    }
}

if (!function_exists('removeAccent')) {
    /**
     * Função responsável por remover acentuação de um texto
     *
     * @param string $value Texto com acentos
     *
     * @access public
     * @return string
     */
    function removeAccent(string $value): string
    {
        return preg_replace(
            ["/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ẽ|ë)/", "/(É|È|Ê|Ẽ|Ë)/", "/(í|ì|î|ĩ|ï)/", "/(Í|Ì|Î|Ĩ|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ũ|ü)/", "/(Ú|Ù|Û|Ũ|Ü)/", "/(ć|ĉ|ç|ḉ)/", "/(Ć|Ĉ|Ç|Ḉ)/", "/(ń|ǹ|ñ)/", "/(Ń|Ǹ|Ñ)/"],
            explode(" ", "a A e E i I o O u U c C n N"),
            $value
        );
    }
}

if (!function_exists('formatToFilename')) {
    /**
     * Função responsável por formatar uma string para ser aceito como nome de arquivo
     *
     * @param string $value Texto a ser formatado
     *
     * @access public
     * @return string
     */
    function formatToFilename(string $value): string
    {
        $value = removeAccent($value);
        $value = mb_strtolower($value, 'UTF-8');
        $replace = [' ', '/'];
        $value = str_replace($replace, "_", $value);

        $remove = ['.', ',', '~', '&', '$', '#', '@', '!', '%', '¨', '*', '=', '+', '§', 'º', 'ª', '?', '>', '<', '|'];
        $value = str_replace($remove, '', $value);

        if (empty($value)) {
            $value = 'filename';
        }

        return $value;
    }
}

if (!function_exists('formatRealToFloat')) {
    /**
     * Função responsável por converter um valor em real (R$ 0,00) para float (0.00)
     *
     * @param string $value Valor em reais
     * @param int $precision Precisão das cadas decimais (Padrão: 2)
     *
     * @access public
     * @return float
     */
    function formatRealToFloat($value, int $precision = 2): float
    {
        return (float)number_format((float)preg_replace(['/[^0-9,-]/', '/[,]/'], ['', '.'], $value), $precision, '.', '');
    }
}

if (!function_exists('formatStringToFloat')) {
    /**
     * Função responsável por converter um valor em string (0,00) para float (0.00)
     *
     * @param string $value Valor a ser convertido
     * @param int $precision Precisão das cadas decimais (Padrão: 2)
     *
     * @access public
     * @return float
     */
    function formatStringToFloat($value, int $precision = 2): float
    {
        $a = strripos($value, ',');
        $b = strripos($value, '.');
        if ($a > $b) {
            $separator = ',';
        } else {
            $separator = '.';
        }
        return (float)number_format((float)preg_replace(["/[^0-9$separator-]/", "/[$separator]/"], ['', '.'], $value), $precision, '.', '');
    }
}

if (!function_exists('formatFloatToReal')) {
    /**
     * Função responsável por converter um float (0.00) em real brasileiro (R$ 0,00)
     *
     * @param float $value Valor em float
     * @param int $precision Precisão das cadas decimais (Padrão: 2)
     *
     * @access public
     * @return string
     */
    function formatFloatToReal($value, int $precision = 2): string
    {
        return 'R$ ' . number_format((float)preg_replace(['/[^0-9.-]/'], [''], $value), $precision, ',', '.');
    }
}

if (!function_exists('formatFloatToValue')) {
    /**
     * Função responsável por converter um float (0.00) em um valor string (0,00)
     *
     * @param float $value Valor em float
     * @param int $precision Precisão das cadas decimais (Padrão: 2)
     *
     * @access public
     * @return string
     */
    function formatFloatToValue($value, int $precision = 2): string
    {
        return number_format((float)preg_replace(['/[^0-9.-]/'], [''], $value), $precision, ',', '.');
    }
}

if (!function_exists('formatDigitableLine')) {
    /**
     * Função responsável por formatar uma string como uma linha digitável
     *
     * @param $value Valor a ser formatado
     */
    function formatDigitableLine($value)
    {
        $oldValue = substr(onlyNumber($value), 0, 48);
        $value = '';
        if (strlen($oldValue) >= 34) {
            $value = substr($oldValue, 0, 5) . "." . substr($oldValue, 5, 5) . " " . substr($oldValue, 10, 5) . "." . substr($oldValue, 15, 6) . " " . substr($oldValue, 21, 5) . "." . substr($oldValue, 26, 6) . " " . substr($oldValue, 32, 1) . " " . substr($oldValue, 33);
        } else if (strlen($oldValue) >= 33) {
            $value = substr($oldValue, 0, 5) . "." . substr($oldValue, 5, 5) . " " . substr($oldValue, 10, 5) . "." . substr($oldValue, 15, 6) . " " . substr($oldValue, 21, 5) . "." . substr($oldValue, 26, 6) . " " . substr($oldValue, 32, 1);
        } else if (strlen($oldValue) >= 27) {
            $value = substr($oldValue, 0, 5) . "." . substr($oldValue, 5, 5) . " " . substr($oldValue, 10, 5) . "." . substr($oldValue, 15, 6) . " " . substr($oldValue, 21, 5) . "." . substr($oldValue, 26, 6);
        } else if (strlen($oldValue) >= 22) {
            $value = substr($oldValue, 0, 5) . "." . substr($oldValue, 5, 5) . " " . substr($oldValue, 10, 5) . "." . substr($oldValue, 15, 6) . " " . substr($oldValue, 21, 5);
        } else if (strlen($oldValue) >= 16) {
            $value = substr($oldValue, 0, 5) . "." . substr($oldValue, 5, 5) . " " . substr($oldValue, 10, 5) . "." . substr($oldValue, 15, 6);
        } else if (strlen($oldValue) >= 11) {
            $value = substr($oldValue, 0, 5) . "." . substr($oldValue, 5, 5) . " " . substr($oldValue, 10, 5);
        } else if (strlen($oldValue) >= 6) {
            $value = substr($oldValue, 0, 5) . "." . substr($oldValue, 5, 5);
        } else {
            $value = substr($oldValue, 0, 5);
        }

        return $value;
    }
}

if (!function_exists('dateEnToBr')) {
    /**
     * Função responsável por converter uma data no formato norte americano (Y-m-d) para o formato brasileiro (d/m/Y)
     *
     * @param string $date Data no formato norte americano
     *
     * @access public
     * @return string
     */
    function dateEnToBr($date): string
    {
        $date = explode(' ', $date);

        $date[0] = explode('-', $date[0]);
        $date[0] = array_reverse($date[0]);
        $date[0] = implode('/', $date[0]);

        if (count($date) > 1) {
            $date = implode(' ', $date);
        } else {
            $date = $date[0];
        }

        return $date;
    }
}

if (!function_exists('dateBrToEn')) {
    /**
     * Função responsável por converter uma data no formato brasileiro (d/m/Y) para o formato norte americano (Y-m-d)
     *
     * @param string $date Data no formato brasileiro
     *
     * @access public
     * @return string
     */
    function dateBrToEn($date)
    {
        $date = explode(' ', $date);

        $date[0] = explode('/', $date[0]);
        $date[0] = array_reverse($date[0]);
        $date[0] = implode('-', $date[0]);

        if (count($date) > 1) {
            $date = implode(' ', $date);
        } else {
            $date = $date[0];
        }

        return $date;
    }
}

if (!function_exists('dateDiff')) {
    /**
     * Função responsável por calcular a diferença entre duas datas
     *
     * @param string $dateOne Data um
     * @param string $dateTwo Data dois
     * @param int $increment Valor a ser incrementado ao valor final (Padrão: 0)
     * @param string $type Tipo de comparação (D - Diferença em dias; M - Diferença em meses (Idenpendente do dia do mês); RealM/AccountingM - Diferença em meses (Considera o dia do mês para o cálculo); Y - Diferença em anos) (Padrão: M)
     *
     * @access public
     * @return string
     */
    function dateDiff(string $dateOne, string $dateTwo, int $increment = 0, string $type = 'M'): string
    {
        $diff = 0;
        switch ($type) {
            case 'D':
                $dateDiff = date_diff(date_create($dateOne), date_create($dateTwo));
                if ($dateDiff->invert == 0) {
                    $diff = $dateDiff->days;
                } else {
                    $diff = ($dateDiff->days * -1);
                }
                break;
            case 'M':
            case 'RealM':
            case 'AccountingM':
                $years = (date('Y', strtotime($dateTwo)) - date('Y', strtotime($dateOne)));
                $months = (date('m', strtotime($dateTwo)) - date('m', strtotime($dateOne)));
                $diff = ($months + ($years * 12));

                if ($type === 'RealM') {
                    if (date('d', strtotime($dateTwo)) < date('d', strtotime($dateOne))) {
                        $diff--;
                    }
                }

                if ($type === 'AccountingM') {
                    if (date('d', strtotime($dateTwo)) > date('d', strtotime($dateOne))) {
                        $diff++;
                    }
                }
                break;
            case 'Y':
                $diff = (date('Y', strtotime($dateTwo)) - date('Y', strtotime($dateOne)));
                break;
            default:
                return 0;
                break;
        }

        return ($diff += $increment);
    }
}

if (!function_exists('isJson')) {
    /**
     * Função responsável por verificar se uma string é um JSON
     *
     * @param string $string Texto a ser verificado
     *
     * @access public
     * @return bool
     */
    function isJson($string): bool
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('')) {
    /**
     * @param string $csvFile Caminho do arquivo CSV
     * @return string Delimitador do arquivo CSV
     */
    function detectCsvDelimiter($csvFile): string
    {
        // Lista os delimitadores
        $delimiters = [";" => 0, "," => 0, "\t" => 0, "|" => 0];

        $handle = fopen($csvFile, "r");
        $firstLine = fgets($handle);
        fclose($handle);
        // Percorre os delimitadores e verifica qual o mais usado
        foreach ($delimiters as $delimiter => &$count) {
            // Conta o número de ocorrências de cada delimitador
            $count = count(str_getcsv($firstLine, $delimiter));
        }
        // Retorna o delimitador com maior ocorrência
        return array_search(max($delimiters), $delimiters);
    }
}

if (!function_exists('readCSV')) {
    /**
     * Função responsável por ler um arquivo CSV e retornar seu conteúdo
     *
     * @param string $filePath Caminho do arquivo
     * @param string $delimiter Delimitador do arquivo (opcional)
     *
     * @access public
     * @return array
     */
    function readCSV($filePath, $delimiter = ''): array
    {
        try {
            if (!$delimiter) $delimiter = detectCsvDelimiter($filePath);

            $reader = new Csv();
            $reader->setDelimiter($delimiter);
            $reader = $reader->load($filePath);
            $rows = [];
            //Itera sobre cada Sheet(Aba) na planilha
            foreach ($reader->getAllSheets() as $sheet) {
                //Itera sobre cada linha de casa Sheet(Aba) na planilha
                foreach ($sheet->toArray() as $row) {
                    $rows[] = $row;
                }
            }

            unset($reader);

            return $rows;
        } catch (\Exception $e) {
            return [];
        }
    }
}

if (!function_exists('readExcel')) {
    /**
     * Função responsável por ler um arquivo XLSX ou XLS e retornar seu conteúdo
     *
     * @param string $filePath Caminho do arquivo
     * @param string $sheet Nome da planilha que deve retornar os dados (opcional)
     *
     * @access public
     * @return array
     */
    function readExcel($filePath, $sheetName = ''): array
    {
        try {
            $reader = IOFactory::load($filePath, 0, [IOFactory::READER_XLS, IOFactory::READER_XLSX]);
            $rows = [];

            foreach ($reader->getAllSheets() as $sheet) {
                $title = $sheet->getTitle();

                foreach ($sheet->toArray() as $row) {
                    $rows[$title][] = $row;
                }
            }

            unset($reader);
        } catch (\Exception $e) {
            return [];
        }

        if ($sheetName) return $rows[$sheetName] ?? [];

        return $rows;
    }
}

if (!function_exists('token64')) {
    /**
     * Função responsável por gerar um token aleatório em base64
     *
     * @access public
     * @return string
     */
    function token64(): string
    {
        $token = '';
        $hash = [1, 2, 3, 9, 8, 7, 6, 5, 4, 5, 8, 2, 9, 3, 6, 4, 7, 1, 3, 5, 7, 9, 5, 1];
        for ($i = 0; $i < 6; $i++) {
            $number = $hash[random_int(0, 23)];
            $token = $token . $number;
        }

        return base64_encode($token);
    }
}

if (!function_exists('incrementMonth')) {
    /**
     * Função responsável por incrementar meses exatos a uma data (Ex.: 2022-01-31 + 1 mês = 2022-02-28)
     *
     * @param string $date Data a ser incrementada
     * @param int|string Dia padrão para o incremento, será o dia utilizado para setar no próximo mês caso o mesmo possua tal dia (Caso não informado pega o da data)
     * @param int $months Quantidade de meses a incrementar (Padrão: 1)
     *
     * @access public
     * @return string
     */
    function incrementMonth(string $date, $day = null, int $months = 1)
    {
        if (empty($day)) {
            $day = date('d', strtotime($date));
        }

        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        $newDate = date('Y-m-' . str_pad($day, 2, '0', STR_PAD_LEFT), strtotime($year . '-' . $month . '-01' . " +$months months"));

        $diff = dateDiff(date('Y-m-d', strtotime($date)), date('Y-m-d', strtotime($newDate)));

        if ($diff == $months) {
            return $newDate;
        } else {
            $soma = (int)$month + $months;
            if ($soma > 12) {
                $diffYears = (int)($soma / 12);
                $year += $diffYears;
                $month = $soma - ($diffYears * 12);
                return date('Y-m-t', strtotime($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01'));
            }
            $month += $months;
            return date('Y-m-t', strtotime($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01'));
        }
    }
}

if (!function_exists('passwordForce')) {
    /**
     * Calcula o nível de força de uma senha (1 - Fraca; 2 - Regular; 6 - Forte; 8 - Muito Forte)
     *
     * @param string $password Senha
     *
     * @access public
     * @return int
     */
    function passwordForce($password)
    {
        $pointLength = 1;
        $pointHas = 1;
        $hasNumber = preg_match('/[0-9]/', $password);
        $hasLower = preg_match('/[a-z]/', $password);
        $hasUpper = preg_match('/[A-Z]/', $password);
        $hasSymbol = preg_match('/[!@#$%&]/', $password);

        if (strlen($password) <= 3) {
            $pointLength = 1;
        } else if (strlen($password) <= 7) {
            $pointLength = 2;
        } else if (strlen($password) <= 12) {
            $pointLength = 6;
        } else {
            $pointLength = 8;
        }

        if ($hasNumber && $hasLower && $hasUpper && $hasSymbol) {
            $pointHas = 8;
        } else if ($hasNumber && $hasLower && $hasUpper) {
            $pointHas = 6;
        } else if (($hasNumber && $hasLower) || ($hasNumber && $hasUpper) || ($hasLower && $hasUpper)) {
            $pointHas = 2;
        }

        return ($pointLength + $pointHas) / 2;
    }
}

if (!function_exists('calcUteis')) {
    /**
     * Função responsável por calcular as quantidade de Dias, Horas e minutos úteis entre dadas (Considerando o horário de expediente 08:00 - 12:00 e 13:30 - 17:30)
     *
     * @param string $date_start Data de início no formato Y-m-d H:i:s
     * @param string $date_end Data de fim no formato Y-m-d H:i:s
     * @param array $holidays Array com os feriados do ano/mês, as datas devem vir no formato Y-m-d (Opcional)
     *
     * @access public
     * @return int
     */
    function calcUteis(string $date_start, string $date_end, $holidays = [])
    {
        $date_start = date('Y-m-d H:i:00', strtotime($date_start));
        $date_end = date('Y-m-d H:i:00', strtotime($date_end));
        $minutes = 0;
        $dtIni = new DateTime($date_start);
        $dtFin = new DateTime($date_end);
        if (in_array(date('H:i:s', strtotime($date_start)), ['08:00:00', '13:30:00'])) {
            $dtFin = new DateTime(date('Y-m-d H:i:s', strtotime('+1 minute', strtotime($date_end))));
        }

        $intervalInMinutes = new DateInterval('PT1M');
        $period = new DatePeriod($dtIni, $intervalInMinutes, $dtFin);
        foreach ($period as $data) {
            /* @var $data \DateTime */
            $dateInMinutes = clone $data;

            /**Caso seja fim de semana ou feriado, ignora */
            if (in_array(date('w', $data->getTimestamp()), [0, 6]) || in_array(date('Y-m-d', $data->getTimestamp()), $holidays)) {
                continue;
            }

            $startFirstRound = clone $dateInMinutes->setTime(8, 0, 0);
            $endFirstRound = clone $dateInMinutes->setTime(12, 0, 0);
            $startSecondRound = clone $dateInMinutes->setTime(13, 30, 0);
            $endSecondRound = clone $dateInMinutes->setTime(17, 30, 0);

            if (($startFirstRound < $data && $data <= $endFirstRound) || ($startSecondRound < $data && $data <= $endSecondRound)) {
                $minutes++;
            }
        }

        return $minutes;
    }
}

if (!function_exists('convertMinutes')) {
    /**
     * Função responsável por converter minutos em Dias, Horas e Minutos
     *
     * @param int $minutos Minutos a serem convertidos
     *
     * @access public
     * @return array
     */
    function convertMinutes(int $minutos)
    {
        /**Pega os dias */
        $horas = intdiv($minutos, 60);
        $dias = intdiv($horas, 8);
        /**Pega as horas */
        $horas = $horas % 8;
        /**Pega os minutos */
        $minutos = $minutos % 60;

        return [
            'minutes' => $minutos,
            'hours' => $horas,
            'days' => $dias
        ];
    }
}

if (!function_exists('calculateDuration')) {
    /**
     * @param string $data Data inicial para o calculo (Obrigatório)
     * @param string $duration Duração em horas a ser calculada (Obrigatório)
     * @param array $holidays Array com os feriados do ano/mês, as datas devem vir no formato Y-m-d (Opcional)
     */
    function calculateDuration($date, $duration, $holidays = [])
    {
        $total_sec = hmsToSecond($duration);
        $total_dias = quorestDiv($total_sec, 28800);
        $data_final = $date;
        if (!empty($total_dias['resto'])) {
            $data_final = date('Y-m-d H:i:s', strtotime($data_final) + $total_dias['resto']);
        }

        $dias = $total_dias['quociente'];
        $result = verifyHour($data_final, $dias);
        $data_final = $result['data_final'];
        $dias = $result['dias'];
        for ($i = 1; $i <= $dias; $i++) {
            $data_final = date('Y-m-d H:i:s', strtotime($data_final . ' +1 day'));

            $result = verifyHour($data_final, $dias);
            $data_final = $result['data_final'];
            $dias = $result['dias'];

            // Tratando feriados, sabados e domingos
            $checkDay = date("d-m", strtotime($data_final));
            $checkDay = explode('-', $checkDay);

            if (in_array(date('w', strtotime($data_final)), [0, 6]) || in_array(date('Y-m-d', strtotime($data_final)), $holidays)) {
                $dias++;
            }
        }

        return $data_final;
    }
}

if (!function_exists('verifyHour')) {
    /**
     * Função responsável por verificar se a hora final calculada bate com o horário final do expediente
     */
    function verifyHour($data_final, $dias)
    {
        // Tratando horas quebradas (que não são 8 horas completas)
        $horario_final = date('H:i:s', strtotime($data_final));
        $exp_inicio = date('H:i:s', strtotime('08:00'));
        $exp_pausa = date('H:i:s', strtotime('12:00'));
        $exp_volta = date('H:i:s', strtotime('13:30'));
        $exp_fim = date('H:i:s', strtotime('17:30'));

        if ($horario_final > $exp_fim) {
            // depois do fim do expediente
            $diferenca = (new DateTime($exp_fim))->diff(new DateTime($horario_final));
            $hora_inicio = date('H:i:s', strtotime('+' . $diferenca->h . ' hour +' . $diferenca->i . ' minutes +' . $diferenca->s . ' seconds', strtotime($exp_inicio)));
            $hora_inicio = strtotime($hora_inicio);
            $hour = date('H', $hora_inicio);
            $minute = date('i', $hora_inicio);
            $sec = date('s', $hora_inicio);

            $data_final = new DateTime($data_final);
            $dias++;
            $data_final_pos = $data_final->setTime($hour, $minute, $sec);
            $data_final = $data_final_pos->format('Y-m-d H:i:s');
        } elseif ($exp_pausa < $horario_final && $horario_final < $exp_volta) {
            // entre intervalo do almoço
            $diferenca = (new DateTime($horario_final))->diff(new DateTime($exp_pausa));
            $hora_volta = date('H:i:s', strtotime('+' . $diferenca->h . ' hour +' . $diferenca->i . ' minutes +' . $diferenca->s . ' seconds', strtotime($exp_volta)));
            $hora_volta = strtotime($hora_volta);
            $hour = date('H', $hora_volta);
            $minute = date('i', $hora_volta);
            $sec = date('s', $hora_volta);

            $data_final = new DateTime($data_final);
            $data_final_pos = $data_final->setTime($hour, $minute, $sec);
            $data_final = $data_final_pos->format('Y-m-d H:i:s');
        } elseif ($horario_final < $exp_inicio) {
            // antes do inicio do expediente
            $diferenca = (new DateTime($exp_inicio))->diff(new DateTime($horario_final));
            $hora_inicio = date('H:i:s', strtotime('+' . $diferenca->h . ' hour +' . $diferenca->i . ' minutes +' . $diferenca->s . ' seconds', strtotime($exp_inicio)));
            $hora_inicio = strtotime($hora_inicio);
            $hour = date('H', $hora_inicio);
            $minute = date('i', $hora_inicio);
            $sec = date('s', $hora_inicio);

            $data_final = new DateTime($data_final);
            $data_final_pos = $data_final->setTime($hour, $minute, $sec);
            $data_final = $data_final_pos->format('Y-m-d H:i:s');
        }

        return [
            'data_final' => $data_final,
            'dias' => $dias
        ];
    }
}

if (!function_exists('hmsToSecond')) {
    /**
     * Formata HH:MM:SS para segundos
     *
     * @param mixed $hora hora para ser formatada
     *
     * @access public
     * @return mixed
     */
    function hmsToSecond($hora)
    {
        if (!$hora) {
            return 0;
        } else {
            sscanf($hora, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_seconds = isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;

            return $time_seconds;
        }
    }
}

if (!function_exists('quorestDiv')) {
    /**
     * Retorna quociente e resto de uma divisão
     *
     * @param mixed $hora hora para ser formatada
     *
     * @access public
     * @return mixed
     */
    function quorestDiv($n, $d)
    {
        if (empty($n) || empty($d)) {
            return [];
        } else {
            return [
                'quociente' => intdiv($n, $d),
                'resto' => $n % $d
            ];
        }
    }
}

if (!function_exists('generateCodeNumeric')) {
    /**
     * Função responsável por gerar um código numérico aleatório
     *
     * @param int $length Tamanho do código gerado
     * @return string
     */
    function generateCodeNumeric(int $length): string
    {
        $numero = '';
        for ($x = 0; $x < $length; $x++) {
            $numero .= rand(0, 9);
        }
        return $numero;
    }
}

if (!function_exists('generateId')) {
    /**
     * Função responsável por gerar um ID baseado em microtime
     *
     * @return string
     */
    function generateId(): string
    {
        return substr(str_replace(',', '', number_format(microtime(true) * 1000000, 0)), 0, 15);
    }
}

if (!function_exists('generatePassword')) {
    /**
     * Função responsável por gerar uma senha aletatória
     *
     * @param int $length Tamanho da senha
     * @param int $contain Tipos de caracteres que a senha deve conter (1 - Números; 2 - Letras; 3 - Números e Letras; 4 - Números, Letras e Simbolos('!@#$%&().'))
     *
     * @return string
     */
    function generatePassword(int $length, int $contain = 3)
    {
        $letrasMinusculas = explode(' ', 'a b c d e f g h i j k l m n o p q r s t u v w x y z');
        $letrasMaiusculas = explode(' ', 'A B C D E F G H I J K L M N O P Q R S T U V W X Y Z');
        $simbolos = explode(' ', '! @ # $ % &');
        $numeros = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        switch ($contain) {
            case 1:
                $hash = [$numeros];
                break;
            case 2:
                $hash = [$letrasMinusculas, $letrasMaiusculas];
                break;
            case 4:
                $hash = [$letrasMinusculas, $numeros, $simbolos, $letrasMaiusculas];
                break;
            default:
                $hash = [$letrasMinusculas, $numeros, $letrasMaiusculas];
                break;
        }


        $password = '';
        for ($x = 0; $x < $length; $x++) {
            $position = rand(0, (count($hash) - 1));
            $subposition = rand(0, (count($hash[$position]) - 1));
            $password .= $hash[$position][$subposition];
        }

        return $password;
    }
}

/**
 * Função faz com que o mês que vier em número, transformar em string (nome do mês passado). Ex: 06 -> "Junho"
 *
 * @param string $date mes atual em string(nome)
 * @return string
 */
if (!function_exists('monthNameBr')) {
    function monthNameBr($month, $abbreviated = false)
    {
        switch ($month) {
            case "01":
                $month = 'Janeiro';
                break;
            case "02":
                $month = 'Fevereiro';
                break;
            case "03":
                $month = 'Março';
                break;
            case "04":
                $month = 'Abril';
                break;
            case "05":
                $month = 'Maio';
                break;
            case "06":
                $month = 'Junho';
                break;
            case "07":
                $month = 'Julho';
                break;
            case "08":
                $month = 'Agosto';
                break;
            case "09":
                $month = 'Setembro';
                break;
            case "10":
                $month = 'Outubro';
                break;
            case "11":
                $month = 'Novembro';
                break;
            case "12":
                $month = 'Dezembro';
                break;
        }

        if ($abbreviated) {
            $month = substr($month, 0, 3);
        }

        return $month;
    }
}

if (!function_exists('incrementBussinessDay')) {
    /**
     * Função responsável por incrementar dias úteis a uma data inicial
     *
     * @param string $data Data inicial para o calculo (Obrigatório)
     * @param string $dias Quantidade de dias a ser incrementado (Obrigatório)
     * @param array $holidays Array com os feriados do ano/mês, as datas devem vir no formato Y-m-d (Opcional)
     */
    function incrementBussinessDay($date, $dias, $holidays = [])
    {;
        $data_final = $date;
        for ($i = 1; $i <= $dias; $i++) {
            $data_final = date('Y-m-d', strtotime($data_final . ' +1 day'));

            // Tratando feriados, sabados e domingos
            $checkDay = date("d-m", strtotime($data_final));
            $checkDay = explode('-', $checkDay);

            if (in_array(date('w', strtotime($data_final)), [0, 6]) || in_array(date('Y-m-d', strtotime($data_final)), $holidays)) {
                $dias++;
            }
        }

        return $data_final;
    }
}

if (!function_exists('decrementBussinessDay')) {
    /**
     * Função responsável por decrementar dias úteis a uma data inicial
     *
     * @param string $data Data inicial para o calculo (Obrigatório)
     * @param string $dias Quantidade de dias a ser decrementado (Obrigatório)
     * @param array $holidays Array com os feriados do ano/mês, as datas devem vir no formato Y-m-d (Opcional)
     */
    function decrementBussinessDay($date, $dias, $holidays = [])
    {;
        $data_final = $date;
        for ($i = 1; $i <= $dias; $i++) {
            $data_final = date('Y-m-d', strtotime($data_final . ' -1 day'));

            // Tratando feriados, sabados e domingos
            $checkDay = date("d-m", strtotime($data_final));
            $checkDay = explode('-', $checkDay);

            if (in_array(date('w', strtotime($data_final)), [0, 6]) || in_array(date('Y-m-d', strtotime($data_final)), $holidays)) {
                $dias++;
            }
        }

        return $data_final;
    }
}

if (!function_exists('isLeapYear')) {
    /**
     * Função responsável por verificar se um ano é bissexto
     *
     * @param integer|string $year Ano a ser conferido
     *
     * @access public
     * @return bool
     */
    function isLeapYear($year = 0): bool
    {
        $div = $year % 4;

        if ($div === 0) {
            $div = $year % 100;
            if ($div === 0) {
                $div = $year % 400;
                if ($div === 0) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('imageCoordinates')) {
    /**
     * Função responsável por extrair as coodernadas de geolocalização de uma imagem
     *
     * @param string $path Caminho até a imagem
     *
     * @access public
     * @return array
     */
    function imageCoordinates(string $path): array
    {
        $fopen = fopen($path, 'rb');
        $exif = exif_read_data($fopen);
        $lat = getCoordinates($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
        $long = getCoordinates($exif["GPSLongitude"], $exif['GPSLongitudeRef']);

        return [
            'lat' => $lat,
            'long' => $long
        ];
    }
}

if (!function_exists('getCoordinates')) {
    /**
     * Função resposável por obter as coodernadas as partir da latitude/longitude, e seu respectivo hemisfério de referência, obtida a partir de um exif
     *
     * @param string $coordenada Coodernada obtida
     * @param string $hemisferio Hemisfério de referência
     *
     * @access public
     * @return string
     */
    function getCoordinates($coordenada, $hemisferio)
    {
        for ($i = 0; $i < 3; $i++) {
            $part = explode('/', $coordenada[$i]);
            if (count($part) == 1) {
                $coordenada[$i] = $part[0];
            } else if (count($part) == 2) {
                $coordenada[$i] = floatval($part[0]) / floatval($part[1]);
            } else {
                $coordenada[$i] = 0;
            }
        }
        list($degrees, $minutes, $seconds) = $coordenada;
        $sign = ($hemisferio == 'W' || $hemisferio == 'S') ? -1 : 1;
        $coord = $sign * ($degrees + $minutes / 60 + $seconds / 3600);
        return $coord;
    }
}

if (!function_exists('calculaVencimentoDigitableLine')) {
    /**
     * Função responsável por calcular o vencimento de uma cobrança pela linha digitável da mesma
     *
     * @param $days Dias obtidos na linha digitável
     *
     * @access public
     * @return string
     */
    function calculaVencimentoDigitableLine($days): string
    {
        $baseBACEN = strtotime('1997-10-07');
        return date('Y-m-d', ($baseBACEN + (+$days + 1) * 24 * 3600));
    }
}

if (!function_exists('sumString')) {
    /**
     * Função responsável por somar os caracteres
     *
     * @param int $number Número a ter seus caracteres somados
     *
     * @access public
     * @return int
     */
    function sumString(int $number)
    {
        $data = str_split($number);
        $soma = 0;

        foreach ($data as $n) {
            $soma += (int)$n;
        }

        return $soma <= 9 ? $soma : sumString($number);
    }
}


if (!function_exists('calculaDACMod10')) {
    /**
     * Função responsável por calcular o DAC em Módulo 10
     *
     * @param string $input Campo a ser calculado
     *
     * @access public
     * @return int
     */
    function calculaDACMod10(string $input)
    {
        $data = str_split($input);
        $data = array_reverse($data);
        $soma = 0;
        foreach ($data as $key => $item) {
            $multiplicador = ($key % 2 ? 1 : 2);
            $result = $item * $multiplicador;

            if ($result > 9) {
                $result = sumString($result);
            }

            $soma += $result;
        }

        $mod = $soma % 10;

        if ($mod == 0) {
            return 0;
        }

        $dezena = (int)(ceil($mod / 10) * 10);

        return $dezena - $mod;
    }
}

if (!function_exists('calculaDACMod11')) {
    /**
     * Função responsável por calcular o DAC em Módulo 11
     *
     * @param string $input Campo a ser calculado
     *
     * @access public
     * @return int
     */
    function calculaDACMod11(string $input)
    {
        $data = str_split($input);
        $data = array_reverse($data);
        $multiplicador = 2;
        $soma = 0;
        foreach ($data as $item) {
            $result = $item * $multiplicador;
            if ($multiplicador < 9) {
                $multiplicador++;
            } else {
                $multiplicador = 2;
            }

            $soma += $result;
        }

        $mod = $soma % 11;
        if (in_array($mod, [0, 1])) {
            return 0;
        } else if ($mod == 10) {
            return 1;
        }

        return 11 - $mod;
    }
}

if (!function_exists('readDigitableLine')) {
    /**
     * Função responsável por extrair as informações de boleto pela linha digitável do mesmo
     *
     * @param string $digitable_line
     * @param boolean $isBarcode Flag para indicar se é um código de barras
     *
     * @access public
     * @return array [
     *      'type' => Tipo do boleto (1 - Bancário | 2 - Consumo),
     *      'barcode' => Código de barras,
     *      'value' => Valor a ser pago,
     *      'due_date' => Data de vencimento
     *      'bank_code' => Código do banco de destino
     * ]
     */
    function readDigitableLine(string $digitable_line, bool $isBarcode): array
    {
        if ($isBarcode) {
            $digitable_line = formatBarcodeToDigitableLine($digitable_line);
        } else {
            $digitable_line = onlyNumber($digitable_line);
        }

        $type = '';
        if (strlen($digitable_line) === 47) {
            $type = 'bancario';
        } else if (strlen($digitable_line) === 48) {
            $type = 'consumo';
        }

        if (empty($type)) {
            return [
                'status' => false,
                'msg' => 'Linha digitável inválida'
            ];
        }

        $campoA = $campoB = $campoC = $campoD = $codBarra = '';
        $digitoA = $digitoB = $digitoC = $digitoD = $digitoGeral = 0;
        $vencimento = '';
        $valor = 0;

        if ($type === 'bancario') {
            $campoA = substr($digitable_line, 0, 9);
            $campoB = substr($digitable_line, 10, 10);
            $campoC = substr($digitable_line, 21, 10);
            $campoD = substr($digitable_line, 33);
            $digitoA = (int)substr($digitable_line, 9, 1);
            $digitoB = (int)substr($digitable_line, 20, 1);
            $digitoC = (int)substr($digitable_line, 31, 1);
            $digitoGeral = (int)substr($digitable_line, 32, 1);
            $codBarra = substr($digitable_line, 0, 4) . $digitoGeral . $campoD . substr($campoA, 4) . $campoB . $campoC;

            if (substr($campoD, 0, 4) != 0) {
                $vencimento = calculaVencimentoDigitableLine(substr($campoD, 0, 4));
            }
            if ((float)substr($campoD, 4, 8) . '.' . substr($campoD, 9) > 0) {
                $valor = substr($campoD, 4, 8) . '.' . substr($campoD, 12);
            }
        } else {
            $campoA = substr($digitable_line, 0, 11);
            $campoB = substr($digitable_line, 12, 11);
            $campoC = substr($digitable_line, 24, 11);
            $campoD = substr($digitable_line, 36, 11);
            $digitoA = (int)substr($digitable_line, 11, 1);
            $digitoB = (int)substr($digitable_line, 23, 1);
            $digitoC = (int)substr($digitable_line, 35, 1);
            $digitoD = (int)substr($digitable_line, 47, 1);
            $digitoGeral = (int)substr($digitable_line, 3, 1);
            $codBarra = $campoA . $campoB . $campoC . $campoD;

            if (substr($campoD, 0, 4) != 0) {
                $vencimento = calculaVencimentoDigitableLine(substr($campoD, 0, 4));
            }
            if ((float)(substr($campoD, 4, 8) . '.' . substr($campoD, 9)) > 0) {
                $valor = substr($codBarra, 4, 9) . '.' . substr($codBarra, 13, 2);
            }
        }

        if ($digitoA != calculaDACMod10($campoA) || $digitoB != calculaDACMod10($campoB) || $digitoC != calculaDACMod10($campoC) || ($type === 'bancario' && $digitoGeral != calculaDACMod11(substr($digitable_line, 0, 4) . $campoD . substr($campoA, 4) . $campoB . $campoC))) {
            return [
                'status' => false,
                'error' => 'Número do boleto é inválido!'
            ];
        }


        if ($type === 'consumo' && ((($digitoD != calculaDACMod10($campoD) || in_array(substr($campoA, 2, 1), ['6', '7'])) && $digitoGeral != calculaDACMod10(substr($campoA, 0, 3) . substr($campoA, 4) . $campoB . $campoC . $campoD)) || (in_array(substr($campoA, 2, 1), ['8', '9']) && $digitoGeral != calculaDACMod11(substr($campoA, 0, 3) . substr($campoA, 4) . $campoB . $campoC . $campoD)))) {
            return [
                'status' => false,
                'error' => 'Número do boleto é inválido!'
            ];
        }

        return [
            'status' => true,
            'data' => [
                'type' => substr($codBarra, 0, 1) != '8' ? 1 : 2,
                'barcode' => $codBarra,
                'value' => (float)$valor,
                'due_date' => $vencimento,
                'bank_code' => substr($codBarra, 0, 3)
            ]
        ];
    }
}

if (!function_exists('formatBarcodeToDigitableLine')) {
    /**
     * Função responsável por converter um código de barras para linha digitável
     *
     * @param string $barcode Código de barras
     *
     * @access public
     * @return string
     */
    function formatBarcodeToDigitableLine(string $barcode)
    {
        $barcode = onlyNumber($barcode);

        if (strlen($barcode) !== 44) {
            return [
                'status' => false,
                'msg' => 'Código de barras inválido'
            ];
        }

        if (substr($barcode, 0, 1) == 8) {
            $type = 'consumo';
        } else {
            $type = 'bancario';
        }

        $campoA = $campoB = $campoC = $campoD = $digitable_line = '';
        $digitoA = $digitoB = $digitoC = $digitoD = $digitoGeral = 0;

        if ($type === 'bancario') {
            $campoA = substr($barcode, 0, 4) . substr($barcode, 19, 5);
            $campoB = substr($barcode, 24, 10);
            $campoC = substr($barcode, 34);
            $campoD = substr($barcode, 5, 14);
            $digitoGeral = substr($barcode, 4, 1);

            $digitoA = calculaDACMod10($campoA);
            $digitoB = calculaDACMod10($campoB);
            $digitoC = calculaDACMod10($campoC);

            $digitable_line = $campoA . $digitoA . $campoB . $digitoB . $campoC . $digitoC . $digitoGeral . $campoD;
        } else {
            $campoA = substr($barcode, 0, 11);
            $campoB = substr($barcode, 11, 11);
            $campoC = substr($barcode, 22, 11);
            $campoD = substr($barcode, 33);

            $digitoA = calculaDACMod10($campoA);
            $digitoB = calculaDACMod10($campoB);
            $digitoC = calculaDACMod10($campoC);
            $digitoD = calculaDACMod10($campoD);

            $digitable_line = $campoA . $digitoA . $campoB . $digitoB . $campoC . $digitoC . $campoD . $digitoD;
        }

        return $digitable_line;
    }
}

if (!function_exists('formatJsonString')) {
    /**
     * Função resposável por retornar os dados de json formatados
     *
     * @param string|array|object $data Dados a serem formatados
     * @param int $step Posição de identação
     *
     * @access public
     * @return string
     */
    function formatJsonString($data, int $step = 1, $br = "\r\n")
    {
        if (is_string($data) && !isJson($data) || (!is_string($data) && !is_object($data) && !is_array($data))) {
            return '"' . $data . '"';
        }

        if (!is_string($data)) {
            $data = json_encode($data);
        }

        $return = '';
        $spaces = $step * 4;
        $dataObject = json_decode($data);

        if (is_object($dataObject)) {
            $dataArray = json_decode(json_encode($dataObject), true);

            $return .= "{" . $br;
            $inputs = [];
            foreach ($dataArray as $key => $value) {
                $input = '';
                $input .= addSpacesString($input, $spaces);

                if (is_array($dataObject->$key) || is_object($dataObject->$key)) {
                    $input .= '"' . $key . '": ' . formatJsonString($value, ($step + 1), $br);
                    $inputs[] = $input;
                    continue;
                }

                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                } else if (!is_null($value)) {
                    if ((int)$value == $value) {
                        $value = (int)$value;
                    } else if ((float)$value == $value) {
                        $value = (float)$value;
                    } else {
                        $value = '"' . $value . '"';
                    }
                } else {
                    $value = 'null';
                }

                $input .= '"' . $key . '": ' . $value;
                $inputs[] = $input;
            }
            $return .= implode(',' . $br, $inputs);
            $return .= $br;
            $return = addSpacesString($return, ($spaces - 4));
            $return .= "}";
        } else if (is_array($dataObject)) {
            $return .= "[" . $br;
            $inputs = [];
            foreach ($dataObject as $value) {
                $input = '';
                $input .= addSpacesString($input, $spaces);
                $input .= formatJsonString($value, ($step + 1), $br);
                $inputs[] = $input;
            }
            $return .= implode(',' . $br, $inputs);
            $return .= $br;
            $return = addSpacesString($return, ($spaces - 4));
            $return .= "]";
        }

        return $return;
    }
}

if (!function_exists('addSpacesString')) {
    /**
     * Função responsável por adicionar espaços a uma string
     *
     * @param string $string Texto a ser adicionado os espaços
     * @param int $spaces Quantidade de espaços
     *
     * @access public
     * @return string
     */
    function addSpacesString(string $string, int $spaces = 1)
    {
        for ($i = 1; $i <= $spaces; $i++) {
            $string .= "&nbsp;";
        }

        return $string;
    }
}
