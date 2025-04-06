<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key
    |--------------------------------------------------------------------------
    |
    | Your OpenAI secret key. Store it in .env as OPENAI_API_KEY.
    |
    */
    'api_key' => env('OPENAI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Chat Model
    |--------------------------------------------------------------------------
    |
    | Default model used for chat completions.
    |
    */
    'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
];
