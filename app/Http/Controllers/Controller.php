<?php

namespace App\Http\Controllers;

abstract class Controller
{
    // Whitelist ekstensi (dicek dari isi file, bukan nama) supaya file .php/.html tidak bisa diunggah ke disk publik
    protected const DOCUMENT_RULE = 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120';
}
