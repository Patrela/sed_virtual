<button {{ $attributes->merge(['type' => 'submit', 'class' => ' main-button ']) }}>
    {{ $slot }}
</button>
