<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CurlController;
use Illuminate\Http\Request;

class ConsultaCEPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($cep)
    {
        $result_query = [];

        $trimmed_ceps = $this->split_ceps($cep);

        foreach ($trimmed_ceps as $cep_raw) {
            $validatedCep = $this->validateCep($cep_raw);

            if (gettype($validatedCep) != 'boolean') {
                return response()->json($validatedCep, 400);
            }

            $curl = new CurlController($cep_raw);

            $retorno = $curl->consulta();
            array_push($result_query, $retorno->original);
        }

        return response()->json($result_query, 200);
    }

    private function split_ceps(string $array_ceps)
    {
        $array_ceps_separated = explode(",", $array_ceps);

        return $array_ceps_separated;
    }

    private function validateCep($cep_raw)
    {
        if (preg_match('/[a-zA-Z\W-]/', $cep_raw)) {
            return "Invalid Cep! There's letters in " . $cep_raw . ".Please remove!";
        } else if (preg_match('/\s/', $cep_raw)) {
            return "Invalid Cep! There's blank space in " . $cep_raw . ".Please fixed!";
        } else if (strlen($cep_raw) != 8) {
            return "Invalid Cep! Digits number is incorrect in " . $cep_raw . ".Please fixed!";
        } else {
            return true;
        }
    }
}
