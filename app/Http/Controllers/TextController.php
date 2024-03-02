<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TextController extends Controller
{
    public function showEncryptForm()
    {
        return view('text.encrypt');
    }

    public function showDecryptForm()
    {
        return view('text.decrypt');
    }

    public function encryptText(Request $request)
    {
        // dd($request);
        $validate = Validator::make($request->all(), [
            "key" => "required|size:16"
        ], [
            "key.required" => "Please enter a key",
            "key.size" => "The key must be 16 characters long"
        ]);

        // Custom validation for text or textFile
        $textProvided = $request->input('text') !== null && trim($request->input('text')) !== '';
        // $fileProvided = $request->hasFile('textFile');

        if ($validate->fails()) {
            return redirect("/text/encrypt")->withErrors($validate);
        }

        // if (!($textProvided xor $fileProvided)) {
        //     $validate->errors()->add('text', 'Please provide either text or a text file, but not both.');
        // }

        if ($validate->fails() || $validate->errors()->isNotEmpty()) {
            return redirect("/text/encrypt")->withErrors($validate);
        }

        // if ($fileProvided) {
        //     $file = $request->file("textFile");
        //     $extension = $file->getClientOriginalExtension();
        //     $text_path = $file->storeAs("public", "text_to_encrypt." . $extension);
        // } else {
        //     $text = $request->input('text');
        // }

        $text = $request->input('text');
        $key = $request->input("key");

        // dd('python "' . base_path() . '/scripts/main.py" -t encrypt -f text -k "' . $key . '" -p "' . $text . '"');
        $output = shell_exec('python "' . base_path() . '/scripts/main.py" -t encrypt -f text -k "' . $key . '" -p "' . $text . '" 2>&1');
        // dd($output);
        $encryptedFileContent = file_get_contents(base_path() . "/storage/app/public/cipher_text.txt");

        return Redirect::route('text.result')->with(["type" => "encryption", "key" => $key, "text" => $text, "ciphertext" => $encryptedFileContent]);
    }


    public function decryptText(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "key" => "required|size:16"
        ], [
            "key.required" => "Please enter a key",
            "key.size" => "The key must be 16 characters long"
        ]);

        // Custom validation for text or textFile
        $textProvided = $request->input('text') !== null && trim($request->input('text')) !== '';
        // $fileProvided = $request->hasFile('textFile');

        if ($validate->fails()) {
            return redirect("/text/decrypt")->withErrors($validate);
        }

        // if (!($textProvided xor $fileProvided)) {
        //     $validate->errors()->add('text', 'Please provide either text or a text file, but not both.');
        // }

        if ($validate->fails() || $validate->errors()->isNotEmpty()) {
            return redirect("/text/decrypt")->withErrors($validate);
        }

        // if ($fileProvided) {
        //     $file = $request->file("textFile");
        //     $extension = $file->getClientOriginalExtension();
        //     $text_path = $file->storeAs("public", "text_to_encrypt." . $extension);
        // } else {
        //     $text = $request->input('text');
        // }

        $text = $request->input('text');
        $key = $request->input("key");

        // dd('python "' . base_path() . '/scripts/main.py" -t encrypt -f text -k "' . $key . '" -p "' . $text . '"');
        $output = shell_exec('python "' . base_path() . '/scripts/main.py" -t decrypt -f text -k "' . $key . '" -p "' . $text . '" 2>&1');

        $decryptedFileContent = file_get_contents(base_path() . "/storage/app/public/plain_text.txt");

        return Redirect::route('text.decrypt-result')->with(["type" => "decryption", "key" => $key, "text" => $text, "plaintext" => $decryptedFileContent]);
    }

    public function showResult(Request $request)
    {
        if (session()->has("type") && session()->has("key")) {
            $type = session("type");
            $key = session("key");
            $text = session("text");
            // $textFile = session("text");
            $textPath = session("text_path");
            $ciphertext = session("ciphertext");

            return view('text.result', [
                "type" => $type,
                "key" => $key,
                "text" => $text,
                // "textfile" => $textFile,
                "ciphertext" => $ciphertext
            ]);
        }
        return Redirect::route('text.view');
    }

    public function showDeResult(Request $request)
    {
        if (session()->has("type") && session()->has("key")) {
            $type = session("type");
            $key = session("key");
            $text = session("text");
            // $textFile = session("text");
            $textPath = session("text_path");
            $plaintext = session("plaintext");


            return view('text.decrypt-result', [
                "type" => $type,
                "key" => $key,
                "text" => $text,
                // "textfile" => $textFile,
                "plaintext" => $plaintext
            ]);
        }
        return Redirect::route('text.view');
    }

    public function showText()
    {
        return view('text.text');
    }
}
