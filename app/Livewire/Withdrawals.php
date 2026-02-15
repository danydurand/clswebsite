<?php

namespace App\Livewire;

use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use App\Domain\Withdrawal\WithdrawalStatusEnum;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

/**
 * Componente Livewire para listar los retiros del cliente autenticado
 * 
 * Este componente muestra una tabla paginada con los retiros del cliente,
 * permitiendo ordenamiento por columnas, filtros y navegación a la página de creación.
 */
class Withdrawals extends Component
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
     * Filtro por status del retiro
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
     * Filtro por método de retiro
     *
     * @var int
     */
    public $methodFilter = 0;

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
     * Resetea la paginación cuando cambia el filtro de método
     *
     * @return void
     */
    public function updatedMethodFilter()
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
        $this->methodFilter = 0;
        $this->resetPage();
    }

    /**
     * Computed property que retorna los retiros filtrados y paginados
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getWithdrawalsProperty()
    {
        $customer = Auth::user()->customer;

        $query = Withdrawal::query()
            ->where('customer_id', $customer->id)
            ->with(['withdrawMethod']);

        // Filtro por status
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Filtro por método
        if ($this->methodFilter > 0) {
            $query->where('withdraw_method_id', $this->methodFilter);
        }

        // Búsqueda por código de transacción
        if (!empty($this->search)) {
            $query->where('trx', 'like', '%' . $this->search . '%');
        }

        // Filtro por rango de fechas
        if (!empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if (!empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // Ordenamiento
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(15);
    }

    /**
     * Computed property que retorna los métodos de retiro activos
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMethodsProperty()
    {
        return WithdrawMethod::where('is_active', true)->get();
    }

    /**
     * Computed property que verifica si hay métodos activos
     *
     * @return bool
     */
    public function getHasActiveMethodsProperty()
    {
        return $this->methods->isNotEmpty();
    }

    /**
     * Renderiza el componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.withdrawals', [
            'withdrawals' => $this->withdrawals,
            'methods' => $this->methods,
            'hasActiveMethods' => $this->hasActiveMethods,
            'statusOptions' => [
                'all' => __('All Statuses'),
                WithdrawalStatusEnum::Initiate->value => WithdrawalStatusEnum::Initiate->getLabel(),
                WithdrawalStatusEnum::Pending->value => WithdrawalStatusEnum::Pending->getLabel(),
                WithdrawalStatusEnum::Success->value => WithdrawalStatusEnum::Success->getLabel(),
                WithdrawalStatusEnum::Reject->value => WithdrawalStatusEnum::Reject->getLabel(),
            ],
        ]);
    }
}
