<?php

namespace App\Models\Traits;

trait JsonData
{
    public function setData($key, $value) {
        // Ensure $this->data is always treated as an array
        $data = is_string($this->data) ? json_decode($this->data, true) : $this->data;
        $data = is_array($data) ? $data : [];

        // Set the key-value pair
        $data[$key] = $value;

        // Store the data as a JSON string
        $this->data = json_encode($data);
    }

    public function getData($key) {
        // Ensure $this->data is always treated as an array
        $data = is_string($this->data) ? json_decode($this->data, true) : $this->data;
        $data = is_array($data) ? $data : [];

        // Return the value or 'N/A' if the key doesn't exist
        return $data[$key] ?? null;
    }

    public function removeData(string $key): void
    {
        // Ensure $this->data is always treated as an array
        $data = is_string($this->data) ? json_decode($this->data, true) : $this->data;
        $data = is_array($data) ? $data : [];

        // Remove the key if it exists
        unset($data[$key]);

        // Store the data as a JSON string
        $this->data = json_encode($data);
    }
}
