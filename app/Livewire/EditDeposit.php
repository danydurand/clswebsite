<?php

namespace App\Livewire;

use App\Models\Deposit;
use App\Models\Gateway;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Domain\Deposit\DepositStatusEnum;
use App\Domain\Deposit\DepositServices;

/**
 * Componente Livewire para editar un depósito existente
 * 
 * Solo permite editar depósitos en estado PENDING
 */
class EditDeposit extends Component
{
    use WithFileUploads;

    /**
     * El depósito a editar
     *
     * @var Deposit
     */
    public Deposit $deposit;

    /**
     * Monto del depósito
     *
     * @var float
     */
    public $amount;

    /**
     * Campos del formulario dinámico
     *
     * @var array
     */
    public $formFields = [];

    /**
     * Datos del formulario
     *
     * @var array
     */
    public $formData = [];

    /**
     * Archivos marcados para eliminación
     *
     * @var array
     */
    public $filesToRemove = [];

    /**
     * Comprobante de pago (se maneja por separado)
     *
     * @var mixed
     */
    public $proof_file = null;

    /**
     * Ruta del comprobante de pago actual
     *
     * @var string|null
     */
    public $current_proof_file = null;

    /**
     * Inicializa el componente y verifica autorización
     *
     * @param Deposit $deposit
     * @return void|\Illuminate\Http\RedirectResponse
     */
    public function mount(Deposit $deposit)
    {
        $user = Auth::user();
        $customer = $user->customer;

        // Verificar que el depósito pertenece al cliente autenticado
        if ($deposit->customer_id !== $customer->id) {
            session()->flash('error', __('You do not have permission to edit this deposit'));
            return redirect()->route('deposits.index');
        }

        // Verificar que el depósito está en estado PENDING
        if ($deposit->status !== DepositStatusEnum::Pending) {
            session()->flash('error', __('Only pending deposits can be edited'));
            return redirect()->route('deposits.view', $deposit->id);
        }

        $this->deposit = $deposit;
        $this->amount = $deposit->amount;

        // Si es un depósito manual, cargar los campos del formulario
        if ($deposit->gateway && $deposit->gateway->isManual()) {
            $this->formFields = $deposit->gateway->getFormFields();

            // Cargar los datos existentes del formulario
            if ($deposit->detail && is_array($deposit->detail) && isset($deposit->detail['form_data'])) {
                $this->formData = $deposit->detail['form_data'];
            }

            // Cargar el comprobante de pago actual si existe
            if ($deposit->detail && isset($deposit->detail['proof_file'])) {
                $this->current_proof_file = $deposit->detail['proof_file'];
            }
        }
    }

    /**
     * Marca un archivo para eliminación
     *
     * @param string $fieldName
     * @return void
     */
    public function removeFile($fieldName)
    {
        if (isset($this->formData[$fieldName])) {
            $this->filesToRemove[] = $fieldName;
            unset($this->formData[$fieldName]);
        }
    }

    /**
     * Elimina el comprobante de pago actual
     *
     * @return void
     */
    public function removeProofFile()
    {
        $this->current_proof_file = null;
    }

    /**
     * Reglas de validación
     *
     * @return array
     */
    protected function rules()
    {
        $rules = [
            'amount' => 'required|numeric|min:0.01',
        ];

        // Agregar reglas de validación para campos del formulario dinámico
        foreach ($this->formFields as $field) {
            $fieldRules = [];

            if (isset($field['required']) && $field['required']) {
                $fieldRules[] = 'required';
            }

            if (isset($field['type'])) {
                switch ($field['type']) {
                    case 'email':
                        $fieldRules[] = 'email';
                        break;
                    case 'number':
                        $fieldRules[] = 'numeric';
                        break;
                    case 'file':
                        if (isset($this->formData[$field['name']]) && is_object($this->formData[$field['name']])) {
                            $fieldRules[] = 'file';
                            $fieldRules[] = 'max:10240'; // 10MB max
                        }
                        break;
                }
            }

            if (!empty($fieldRules)) {
                $rules['formData.' . $field['name']] = implode('|', $fieldRules);
            }
        }

        return $rules;
    }

    /**
     * Actualiza el depósito
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->validate();

        try {
            // Preparar datos para actualizar
            $updateData = [
                'amount' => $this->amount,
            ];

            // Si hay campos del formulario (depósito manual), actualizar los detalles
            if (!empty($this->formData) || !empty($this->formFields)) {
                // Mantener la estructura existente de detail
                $existingDetail = $this->deposit->detail ?? [];

                // Procesar archivos nuevos
                foreach ($this->formData as $fieldName => $value) {
                    // Si es un archivo nuevo (UploadedFile), guardarlo
                    if (is_object($value) && method_exists($value, 'store')) {
                        $path = $value->store('deposit-files', 'public');
                        $this->formData[$fieldName] = $path;
                    }
                }

                // Actualizar form_data
                $existingDetail['form_data'] = $this->formData;

                // Manejar proof_file por separado
                if ($this->proof_file) {
                    // Nuevo archivo de comprobante
                    $proofPath = $this->proof_file->store('deposit-proofs', 'public');
                    $existingDetail['proof_file'] = $proofPath;
                } elseif ($this->current_proof_file === null && isset($existingDetail['proof_file'])) {
                    // Se eliminó el comprobante
                    unset($existingDetail['proof_file']);
                } elseif ($this->current_proof_file) {
                    // Mantener el comprobante actual
                    $existingDetail['proof_file'] = $this->current_proof_file;
                }

                $updateData['detail'] = $existingDetail;
            }

            // Actualizar el depósito usando el servicio
            DepositServices::updateDeposit($this->deposit, $updateData);

            session()->flash('success', __('Deposit updated successfully'));
            return redirect()->route('deposits.view', $this->deposit->id);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('deposits.view', $this->deposit->id);
        }
    }

    /**
     * Renderiza el componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.edit-deposit');
    }
}
