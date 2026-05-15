<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl leading-tight text-ink">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="space-y-6 px-4 sm:px-6 xl:px-10 2xl:px-14">
            <div class="wayna-card p-4 sm:p-8">
                <div class="max-w-3xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="wayna-card p-4 sm:p-8">
                <div class="max-w-3xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="wayna-card p-4 sm:p-8">
                <div class="max-w-3xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
