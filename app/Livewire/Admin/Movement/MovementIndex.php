<?php

namespace App\Livewire\Admin\Movement;

use App\Models\User;
use App\Models\Movement;
use App\Livewire\Forms\MovementForm;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Url, Computed};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovementsExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MovementIndex extends Component
{
    use WithPagination;

    public MovementForm $form;

    #[Url(history: true)]
    public string $search = '';

    #[Url]
    public string $selectedMonth = '';

    public bool $showingModal = false;

    /**
     * Initialize defaults.
     */
    public function mount(): void
    {
        $this->selectedMonth = $this->selectedMonth ?: now()->format('Y-m');
    }

    /**
     * Reset pagination when search query changes.
     */
    public function updatedSearch(): void 
    { 
        $this->resetPage(); 
    }

    /**
     * Using #[Computed] caches the user list for the duration of a single request,
     * which is more efficient for Blade rendering.
     */
    #[Computed]
    public function users(): Collection
    {
        return User::query()
            ->select(['id', 'name', 'employee_id'])
            ->orderBy('name')
            ->get();
    }

    public function create(): void
    {
        $this->form->reset();
        // PHP 8.4 property hooks can also be used inside Form Objects if needed
        $this->form->started_at = now()->format('Y-m-d\TH:i');
        $this->showingModal = true;
    }

    public function edit(Movement $movement): void
    {
        $this->form->set($movement);
        $this->showingModal = true;
    }

    public function save(): void
    {
        // Using the null-safe operator or explicit check for clarity
        $this->form->movement ? $this->form->update() : $this->form->store();
        
        $this->showingModal = false;
        $this->dispatch('notify', message: 'Movement record saved.');
    }

    public function delete(Movement $movement): void
    {
        $movement->delete();
        $this->dispatch('notify', message: 'Movement record removed.');
    }

    public function exportExcel(): BinaryFileResponse
    {
        $filename = "movements-{$this->selectedMonth}.xlsx";
        return Excel::download(new MovementsExport($this->selectedMonth), $filename);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.admin.movement.movement-index', [
            'movements' => Movement::query()
                // 1. Eager load user including those that are soft-deleted
                ->with(['user' => fn($q) => $q->withTrashed()]) 
                ->when($this->search, function($query) {
                    // 2. Allow searching through deleted users too
                    $query->whereHas('user', function($q) {
                        $q->withTrashed() 
                        ->where(function($inner) {
                            $inner->where('name', 'like', "%{$this->search}%")
                                    ->orWhere('employee_id', 'like', "%{$this->search}%");
                        });
                    });
                })
                ->latest('started_at')
                ->paginate(15),
            
            // Droplist should stay clean (only active users for new logs)
            'users' => User::orderBy('name')->select('id', 'name', 'employee_id')->get(),
        ]);
    }
}
