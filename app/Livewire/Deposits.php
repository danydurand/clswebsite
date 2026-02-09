<?php

namespace App\Livewire;

use App\Models\Deposit;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

/**
 * Componente Livewire para listar los depósitos del cliente autenticado
 * 
 * Este componente muestra una tabla paginada con los depósitos del cliente,
 * permitiendo ordenamiento por columnas y navegación a la página de creación.
 */
class Deposits extends Component
{
    use WithPagination;

    /**
     * Columna por la cual ordenar los resultados
     *
     * @var string
     */
    public $sortBy = 'created_at';

    /**
     * Dirección del ordenamiento (asc o desc)
     *
     * @var string
     */
    public $sortDirection = 'desc';

    /**
     * Maneja el ordenamiento de columnas
     * 
     * Si se hace clic en la misma columna, invierte la dirección.
     * Si se hace clic en una columna diferente, ordena ascendentemente.
     *
     * @param string $column Nombre de la columna a ordenar
     * @return void
     */
    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Obtiene los depósitos del cliente autenticado
     * 
     * Retorna una colección paginada de depósitos ordenados según
     * las propiedades $sortBy y $sortDirection.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    #[\Livewire\Attributes\Computed]
    public function deposits()
    {
        $user = Auth::user();
        $customer = $user->customer;

        return Deposit::query()
            ->where('customer_id', $customer->id)
            ->with(['gateway'])
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(5);
    }

    /**
     * Renderiza el componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.deposits', [
            'deposits' => $this->deposits,
        ]);
    }
}
