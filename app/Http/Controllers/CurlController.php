<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurlController extends Controller
{
    private $cep;
    public function __construct($cep)
    {
        $this->cep = $cep;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function consulta()
    {
        $link_api = env('LINK_VIA_CEP');
        $curl = curl_init();

        try {
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $link_api . $this->cep . "/json"
            ]);

            // Envio e armazenamento da resposta
            $response = curl_exec($curl);

            // Fecha e limpa recursos
            curl_close($curl);
            $retorno = json_decode($response, false);
            $retorno = $this->createLabelField($retorno->logradouro, $retorno->localidade, $retorno);
            return response()->json($retorno, 200);
        } catch (\Throwable $th) {
            //throw $th;
            echo "Erro " . $th;
        }
    }

    private function createLabelField($logradouro, $localidade, $retorno)
    {
        $label = $logradouro . "," . " ".$localidade;
        $retorno->label = $label;
        return $retorno;
    }
}
