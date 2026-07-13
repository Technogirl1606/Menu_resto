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
    <div class="min-h-screen bg-white">
        <!-- En-tête + onglets : fixes en haut au scroll -->
        <div class="sticky top-0 z-10 shadow-sm">
            <div class="bg-red-100 text-center py-4 sm:py-6 px-5">
                <img src="/logo.png" alt="logo" class="h-12 sm:h-16 mx-auto object-contain" />
                <p class="text-red-600 text-xs sm:text-sm mt-1">Voici notre menu d'aujourd'hui. Bon appétit !</p>
            </div>

            <div class="bg-white border-b border-gray-100">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex gap-2 overflow-x-auto py-3 sm:justify-center sm:flex-wrap">
                        <button
                            v-for="cat in categories"
                            :key="cat.id"
                            @click="activeId = cat.id"
                            class="whitespace-nowrap text-sm sm:text-base px-4 py-2 rounded-full border transition-colors"
                            :class="cat.id === activeId
                                ? 'bg-red-600 border-red-600 text-white'
                                : 'bg-transparent border-gray-200 text-gray-700 hover:border-red-300'"
                        >
                            {{ cat.name }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Grille des plats -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6 py-6 min-h-[220px]">
                <div
                    v-for="item in activeCategory?.items ?? []"
                    :key="item.id"
                    class="border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow"
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
                    <div class="p-3 sm:p-4">
                        <p class="font-medium text-sm sm:text-base text-gray-900">{{ item.name }}</p>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1.5">{{ item.description }}</p>
                        <p class="text-sm sm:text-base font-medium text-red-600">{{ item.price }} F</p>
                    </div>
                </div>

                <p v-if="!activeCategory?.items?.length" class="col-span-2 sm:col-span-3 lg:col-span-4 xl:col-span-5 text-center text-sm text-gray-400 py-8">
                    Aucun plat disponible dans cette catégorie pour le moment.
                </p>
            </div>
        </div>
    </div>
</template>