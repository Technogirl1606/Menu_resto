<script setup lang="ts">
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    categories: Array<{
        id: number;
        name: string;
        slug: string;
        available_from: string | null;
        available_to: string | null;
    }>;
}>();

/* --- Ajout --- */
const form = useForm({
    name: '',
    available_from: '',
    available_to: '',
});

function submit() {
    form.post('/admin/categories', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

/* --- Édition --- */
const editingId = ref<number | null>(null);

const editForm = useForm({
    name: '',
    available_from: '',
    available_to: '',
});

function startEdit(cat: { id: number; slug: string; name: string; available_from: string | null; available_to: string | null }) {
    editingId.value = cat.id;
    editForm.name = cat.name;
    editForm.available_from = cat.available_from ?? '';
    editForm.available_to = cat.available_to ?? '';
}

function cancelEdit() {
    editingId.value = null;
    editForm.reset();
}

function saveEdit(slug: string) {
    editForm.put(`/admin/categories/${slug}`, {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
}

function clearHours(slug: string, name: string) {
    editForm.name = name;
    editForm.available_from = '';
    editForm.available_to = '';
    editForm.put(`/admin/categories/${slug}`, {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
}
/* --- Suppression --- */
function destroy(category: { id: number; slug: string; name: string }) {
    if (confirm(`Supprimer la catégorie "${category.name}" et tous ses plats ?`)) {
        router.delete(`/admin/categories/${category.slug}`);
    }
}
</script>

<template>
    <Head title="Catégories" />

    <div class="max-w-2xl mx-auto py-8 px-4">
        <h1 class="text-xl font-semibold text-foreground mb-6">Catégories</h1>

        <!-- Ajout -->
        <form @submit.prevent="submit" class="bg-card border border-border rounded-xl p-4 mb-6 space-y-3">
            <input
                v-model="form.name"
                type="text"
                placeholder="Nom de la nouvelle catégorie (ex: Menu enfant)"
                class="w-full bg-background border border-input rounded-md px-3 py-2 text-sm text-foreground"
            />
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-muted-foreground mb-1">Disponible de</label>
                    <input v-model="form.available_from" type="time" class="bg-background border border-input rounded-md px-2 py-1.5 text-sm text-foreground" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted-foreground mb-1">à</label>
                    <input v-model="form.available_to" type="time" class="bg-background border border-input rounded-md px-2 py-1.5 text-sm text-foreground" />
                </div>
                <button type="submit" class="text-xs px-3 py-2 rounded-md bg-primary text-primary-foreground" :disabled="form.processing">
                    Ajouter
                </button>
            </div>
            <p class="text-xs text-muted-foreground">Laisse les horaires vides si la catégorie doit être visible toute la journée.</p>
            <p v-if="form.errors.name" class="text-destructive text-xs">{{ form.errors.name }}</p>
        </form>

        <!-- Liste -->
        <div class="space-y-2">
            <div v-for="cat in categories" :key="cat.id" class="bg-card border border-border rounded-lg p-3">
                <div v-if="editingId !== cat.id" class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-medium text-foreground">{{ cat.name }}</span>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            <span v-if="cat.available_from && cat.available_to">
                                {{ cat.available_from }} – {{ cat.available_to }}
                            </span>
                            <span v-else>Toute la journée</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Link
                            :href="`/admin/items/${cat.slug}`"
                            class="text-xs px-2.5 py-1.5 border border-input rounded-md text-foreground hover:bg-muted"
                        >
                            Voir les plats
                        </Link>
                        <button @click="startEdit(cat)" class="text-xs px-2.5 py-1.5 border border-input rounded-md text-foreground hover:bg-muted">
                            Modifier
                        </button>
                        <button @click="destroy(cat)" class="text-xs px-2.5 py-1.5 border border-destructive/30 rounded-md text-destructive hover:bg-destructive/10">
                            Supprimer
                        </button>
                    </div>
                </div>

                <!-- Mode édition -->
                <div v-else class="space-y-3">
                    <input
                        v-model="editForm.name"
                        type="text"
                        class="w-full bg-background border border-input rounded-md px-3 py-2 text-sm text-foreground"
                    />
                    <div class="flex flex-wrap items-end gap-3">
                        <div>
                            <label class="block text-xs font-medium text-muted-foreground mb-1">Disponible de</label>
                            <input v-model="editForm.available_from" type="time" class="bg-background border border-input rounded-md px-2 py-1.5 text-sm text-foreground" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-muted-foreground mb-1">à</label>
                            <input v-model="editForm.available_to" type="time" class="bg-background border border-input rounded-md px-2 py-1.5 text-sm text-foreground" />
                        </div>
                        <button @click="saveEdit(cat.slug)" class="text-xs px-3 py-2 rounded-md bg-primary text-primary-foreground">
                            Enregistrer
                        </button>
                        <button @click="clearHours(cat.slug, cat.name)" class="text-xs px-3 py-2 rounded-md border border-destructive/30 text-destructive hover:bg-destructive/10">
                            Retirer les horaires
                        </button>
                        <button @click="cancelEdit" class="text-xs px-3 py-2 rounded-md border border-input text-foreground hover:bg-muted">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>

            <p v-if="!categories.length" class="text-sm text-muted-foreground text-center py-6">
                Aucune catégorie pour l'instant.
            </p>
        </div>
    </div>
</template>