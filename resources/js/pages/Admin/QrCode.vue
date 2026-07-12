<script setup>
defineProps({
    menuUrl: { type: String, required: true },
});
</script>

<template>
    <div class="max-w-md mx-auto py-10 px-4 text-center">
        <h1 class="text-xl font-medium mb-1">QR code du menu</h1>
        <p class="text-sm text-gray-500 mb-6">
            À imprimer et poser sur les tables. Il pointe vers :
            <br />
            <span class="font-mono text-red-600">{{ menuUrl }}</span>
        </p>

        <div class="border border-gray-200 rounded-xl p-6 inline-block">
            <img src="/admin/qr-code/image" alt="QR code du menu" class="w-64 h-64" />
        </div>

        <div class="mt-4">
            <a
                href="/admin/qr-code/image"
                download="qr-code-menu.png"
                class="inline-block bg-red-600 text-white text-sm px-4 py-2 rounded"
            >
                Télécharger le QR code
            </a>
        </div>
    </div>
</template>