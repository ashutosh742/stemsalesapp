<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChatAI_model extends CI_Model {

     public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
    }


// *********************************************************************************************************************************

 public  function call_chatgpt_api($message, $previousMessages) {
        // Your ChatGPT API integration
        $api_key = $this->config->item('openai_api_key');
        $api_url = 'https://api.openai.com/v1/chat/completions';
        
        // Prepare messages for ChatGPT
        $messages = [];
        
        // Add system message for context
        $messages[] = [
            'role' => 'system',
            'content' => 'You are a Business Analytics AI assistant. Provide detailed, insightful analysis based on the data provided. Format your response with clear sections, bullet points where appropriate, and actionable recommendations.'
        ];
        
        // Add previous messages for context
        foreach($previousMessages as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }
        
        // Add current message
        $messages[] = ['role' => 'user', 'content' => $message];
        
        // $data = [
        //     'model' => 'gpt-3.5-turbo',
        //     'messages' => $messages,
        //     'max_tokens' => 2000,
        //     'temperature' => 0.7
        // ];


        $data = [
            'model' => 'gpt-4o',
            'messages' => $messages,

            // Give it breathing space for reasoning
            'max_tokens' => 8192,

            // Lower temperature = better logic, less nonsense
            'temperature' => 0.3,

            // Optional but useful for structured thinking
            'top_p' => 0.9,

            // Keeps replies focused instead of rambling
            'presence_penalty' => 0.2,
            'frequency_penalty' => 0.2
        ];


        
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ]);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        $result_data = json_decode($result, true);
        
        if (isset($result_data['choices'][0]['message']['content'])) {
            return $result_data['choices'][0]['message']['content'];
        }
        
        // Fallback response if API fails
        return $this->generate_fallback_response($message);
    }



        public  function generate_fallback_response($message) {
            $responses = [
                "I've analyzed the data for the selected period. Here are the key insights based on available metrics.",
                "Based on the business data analysis, here are the performance highlights and recommendations.",
                "The analysis reveals important trends and patterns in the selected time period. Here's a summary.",
                "Here's my analysis of the business metrics for the specified date range."
            ];
            
            return $responses[array_rand($responses)] . "\n\nPlease ensure your ChatGPT API key is properly configured for more detailed analysis.";
        }



}

