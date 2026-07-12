<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    categories: { type: Array, required: true },
});

const activeId = ref(props.categories[0]?.id ?? null);

const activeCategory = computed(() =>
    props.categories.find((c) => c.id === activeId.value)
);
</script>

<template>
    <div class="min-h-screen bg-black py-6 px-3">
        <div class="max-w-md mx-auto rounded-3xl bg-white overflow-hidden">
            <!-- En-tête -->
            <div class="bg-red-600 text-center py-4 px-5">
                <p class="text-white font-medium text-lg">Chez Fatou</p>
                <p class="text-red-100 text-xs mt-0.5"> scannez pour commander</p>
            </div>

            <!-- Onglets des catégories -->
            <div class="flex gap-1.5 overflow-x-auto px-2.5 pt-2.5">
                <button
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="activeId = cat.id"
                    class="whitespace-nowrap text-sm px-3 py-1.5 rounded-full border transition-colors"
                    :class="cat.id === activeId
                        ? 'bg-red-600 border-red-600 text-white'
                        : 'bg-transparent border-gray-200 text-gray-700'"
                >
                    {{ cat.name }}
                </button>
            </div>

            <!-- Grille des plats -->
            <div class="grid grid-cols-2 gap-2.5 p-3 min-h-55">
                <div
                    v-for="item in activeCategory?.items ?? []"
                    :key="item.id"
                    class="border border-gray-100 rounded-lg overflow-hidden"
                >
                    <div class="w-full aspect-square bg-red-50 flex items-center justify-center overflow-hidden">
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            :alt="item.name"
                            class="w-full h-full object-cover"
                        />
                        <span v-else class="text-red-300 text-xs">Pas de photo</span>
                    </div>
                    <div class="p-2">
                        <p class="font-medium text-sm text-gray-900">{{ item.name }}</p>
                        <p class="text-xs text-gray-500 mb-1.5">{{ item.description }}</p>
                        <p class="text-sm font-medium text-red-600">{{ item.price }} F</p>
                    </div>
                </div>

                <p v-if="!activeCategory?.items?.length" class="col-span-2 text-center text-sm text-gray-400 py-8">
                    Aucun plat disponible dans cette catégorie pour le moment.
                </p>
            </div>
        </div>
    </div>
</template>