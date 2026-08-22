<script setup lang="ts">
import { Head, useForm, router, Link } from '@inertiajs/vue3';

defineProps<{
    categories: Array<{ id: number; name: string; slug: string; is_active: boolean }>;
}>();

const form = useForm({ name: '' });

function submit() {
    form.post('/admin/categories', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function destroy(category: { id: number; name: string }) {
    if (confirm(`Supprimer la catégorie "${category.name}" et tous ses plats ?`)) {
        router.delete(`/admin/categories/${category.id}`);
    }
}
</script>

<template>
    <Head title="Catégories" />

    <div class="max-w-2xl mx-auto py-8 px-4">
        <h1 class="text-xl font-semibold text-foreground mb-6">Catégories</h1>

        <form @submit.prevent="submit" class="bg-card border border-border rounded-xl p-4 mb-6 flex gap-2 items-center">
            <input
                v-model="form.name"
                type="text"
                placeholder="Nom de la nouvelle catégorie (ex: Menu enfant)"
                class="flex-1 bg-background border border-input rounded-md px-3 py-2 text-sm text-foreground"
            />
            <button type="submit" class="text-xs px-3 py-2 rounded-md bg-primary text-primary-foreground" :disabled="form.processing">
                Ajouter
            </button>
        </form>
        <p v-if="form.errors.name" class="text-destructive text-xs -mt-4 mb-4">{{ form.errors.name }}</p>

        <div class="space-y-2">
            <div
                v-for="cat in categories"
                :key="cat.id"
                class="flex items-center justify-between bg-card border border-border rounded-lg p-3"
            >
                <span class="text-sm font-medium text-foreground">{{ cat.name }}</span>
                <div class="flex items-center gap-2">
                    <Link
                        :href="`/admin/items/${cat.slug}`"
                        class="text-xs px-2.5 py-1.5 border border-input rounded-md text-foreground hover:bg-muted"
                    >
                        Voir les plats
                    </Link>
                    <button
                        @click="destroy(cat)"
                        class="text-xs px-2.5 py-1.5 border border-destructive/30 rounded-md text-destructive hover:bg-destructive/10"
                    >
                        Supprimer
                    </button>
                </div>
            </div>

            <p v-if="!categories.length" class="text-sm text-muted-foreground text-center py-6">
                Aucune catégorie pour l'instant.
            </p>
        </div>
    </div>
</template>