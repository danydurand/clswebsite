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
     * Filtro por status del depósito
     *
     * @var string
     */
    public $statusFilter = 'all';

    /**
     * Término de búsqueda por código de transacción
     *
     * @var string
     */
    public $search = '';

    /**
     * Fecha de inicio para filtro de rango
     *
     * @var string|null
     */
    public $dateFrom = null;

    /**
     * Fecha de fin para filtro de rango
     *
     * @var string|null
     */
    public $dateTo = null;

    /**
     * Filtro por gateway
     *
     * @var string
     */
    public $gatewayFilter = 'all';

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
     * Resetea la paginación cuando cambia el filtro de status
     *
     * @return void
     */
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Resetea la paginación cuando cambia el término de búsqueda
     *
     * @return void
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Limpia todos los filtros
     *
     * @return void
     */
    public function clearFilters()
    {
        $this->statusFilter = 'all';
        $this->search = '';
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->gatewayFilter = 'all';
        $this->resetPage();
    }

    /**
     * Resetea la paginación cuando cambia el filtro de fecha desde
     *
     * @return void
     */
    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    /**
     * Resetea la paginación cuando cambia el filtro de fecha hasta
     *
     * @return void
     */
    public function updatedDateTo()
    {
        $this->resetPage();
    }

    /**
     * Resetea la paginación cuando cambia el filtro de gateway
     *
     * @return void
     */
    public function updatedGatewayFilter()
    {
        $this->resetPage();
    }

    /**
     * Obtiene los gateways que han sido utilizados por el cliente
     *
     * @return \Illuminate\Support\Collection
     */
    #[\Livewire\Attributes\Computed]
    public function usedGateways()
    {
        $user = Auth::user();
        $customer = $user->customer;

        return \App\Models\Gateway::query()
            ->whereHas('deposits', function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            })
            ->orderBy('name')
            ->get();
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
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->search, function ($query) {
                $query->where('trx', 'LIKE', '%' . $this->search . '%');
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->gatewayFilter !== 'all', function ($query) {
                $query->where('gateway_id', $this->gatewayFilter);
            })
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
