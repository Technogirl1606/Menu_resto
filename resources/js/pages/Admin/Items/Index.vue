<script setup>
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    activeCategory: { type: Object, required: true },
    items: { type: Array, required: true },
    categoryOptions: { type: Array, required: true },
});

const editingId = ref(null); // null = mode "ajout", sinon id du plat en cours de modification
const imagePreview = ref(null);

const form = useForm({
    category_id: props.activeCategory.id,
    name: '',
    description: '',
    price: '',
    is_available: true,
    image: null,
});

// Si on change de catégorie (clic dans la sidebar), le formulaire d'ajout
// pré-sélectionne automatiquement la nouvelle catégorie active.
watch(() => props.activeCategory.id, (id) => {
    if (!editingId.value) form.category_id = id;
});

function onImageChange(e) {
    const file = e.target.files[0];
    form.image = file ?? null;
    imagePreview.value = file ? URL.createObjectURL(file) : null;
}

function startEdit(item) {
    editingId.value = item.id;
    form.category_id = item.category_id;
    form.name = item.name;
    form.description = item.description ?? '';
    form.price = item.price;
    form.is_available = item.is_available;
    form.image = null;
    imagePreview.value = item.image_url;
}

function cancelEdit() {
    editingId.value = null;
    form.reset();
    form.category_id = props.activeCategory.id;
    imagePreview.value = null;
}

function submit() {
    if (editingId.value) {
        form.post(`/admin/items/${editingId.value}/update`, {
            forceFormData: true,
            onSuccess: () => cancelEdit(),
        });
    } else {
        form.post('/admin/items', {
            forceFormData: true,
            onSuccess: () => cancelEdit(),
        });
    }
}

function destroy(item) {
    if (confirm(`Supprimer "${item.name}" ?`)) {
        router.delete(`/admin/items/${item.id}`);
    }
}
</script>

<template>
    <div class="max-w-3xl mx-auto py-8 px-4">
        <h1 class="text-xl font-semibold text-foreground mb-6">{{ activeCategory.name }}</h1>

        <!-- Formulaire ajout / modification -->
        <form
            @submit.prevent="submit"
            class="bg-card border border-border rounded-xl p-5 sm:p-6 mb-8 space-y-4 shadow-sm"
        >
            <p class="font-semibold text-base text-foreground">
                {{ editingId ? 'Modifier le plat' : 'Ajouter un plat' }}
            </p>

            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Catégorie</label>
                <select
                    v-model="form.category_id"
                    class="w-full bg-background border border-input rounded-md px-3 py-2 text-sm text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                >
                    <option v-for="cat in categoryOptions" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Nom du plat</label>
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Ex : Thiéboudienne"
                    class="w-full bg-background border border-input rounded-md px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Description</label>
                <textarea
                    v-model="form.description"
                    placeholder="Ex : Riz au poisson, légumes"
                    class="w-full bg-background border border-input rounded-md px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                ></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Prix (F CFA)</label>
                <input
                    v-model="form.price"
                    type="number"
                    step="0.01"
                    placeholder="Ex : 2500"
                    class="w-full bg-background border border-input rounded-md px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-1.5">Photo du plat</label>
                <div class="flex items-center gap-3">
                    <div class="w-20 h-20 flex-shrink-0 rounded-lg border border-border bg-muted overflow-hidden flex items-center justify-center">
                        <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                        <span v-else class="text-muted-foreground text-xs">Aperçu</span>
                    </div>
                    <label class="cursor-pointer text-sm border border-input rounded-md px-3 py-2 text-foreground hover:bg-muted transition-colors">
                        Choisir une photo
                        <input type="file" accept="image/*" @change="onImageChange" class="hidden" />
                    </label>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-foreground">
                <input v-model="form.is_available" type="checkbox" class="rounded border-input" />
                Disponible
            </label>

            <div class="flex gap-2 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-primary text-primary-foreground text-sm font-medium px-4 py-2 rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50"
                >
                    {{ editingId ? 'Enregistrer' : 'Ajouter' }}
                </button>
                <button
                    v-if="editingId"
                    type="button"
                    @click="cancelEdit"
                    class="text-sm px-4 py-2 rounded-md border border-input text-foreground hover:bg-muted transition-colors"
                >
                    Annuler
                </button>
            </div>

            <p v-if="form.errors.name" class="text-destructive text-xs">{{ form.errors.name }}</p>
            <p v-if="form.errors.image" class="text-destructive text-xs">{{ form.errors.image }}</p>
        </form>

        <!-- Liste des plats de la catégorie active -->
        <div class="space-y-2">
            <div
                v-for="item in items"
                :key="item.id"
                class="flex flex-col sm:flex-row sm:items-center gap-3 bg-card border border-border rounded-lg p-3"
            >
                <div class="flex items-center gap-3 min-w-0">
                    <img v-if="item.image_url" :src="item.image_url" class="w-12 h-12 object-cover rounded-md flex-shrink-0" />
                    <div v-else class="w-12 h-12 bg-muted rounded-md flex-shrink-0"></div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-foreground truncate">{{ item.name }}</p>
                        <p class="text-xs text-muted-foreground">{{ item.price }} F</p>
                    </div>

                    <span
                        class="text-xs px-2 py-1 rounded-full whitespace-nowrap sm:hidden"
                        :class="item.is_available
                            ? 'bg-green-500/10 text-green-600 dark:text-green-400'
                            : 'bg-muted text-muted-foreground'"
                    >
                        {{ item.is_available ? 'Disponible' : 'Masqué' }}
                    </span>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0 sm:ml-auto">
                    <span
                        class="hidden sm:inline text-xs px-2 py-1 rounded-full whitespace-nowrap"
                        :class="item.is_available
                            ? 'bg-green-500/10 text-green-600 dark:text-green-400'
                            : 'bg-muted text-muted-foreground'"
                    >
                        {{ item.is_available ? 'Disponible' : 'Masqué' }}
                    </span>

                    <button
                        @click="startEdit(item)"
                        class="flex-1 sm:flex-none text-xs px-2.5 py-1.5 border border-input rounded-md text-foreground hover:bg-muted transition-colors"
                    >
                        Modifier
                    </button>
                    <button
                        @click="destroy(item)"
                        class="flex-1 sm:flex-none text-xs px-2.5 py-1.5 border border-destructive/30 rounded-md text-destructive hover:bg-destructive/10 transition-colors"
                    >
                        Supprimer
                    </button>
                </div>
            </div>

            <p v-if="!items.length" class="text-sm text-muted-foreground text-center py-6">
                Aucun plat dans cette catégorie pour l'instant.
            </p>
        </div>
    </div>
</template>