<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm as useInertiaForm } from '@inertiajs/vue3'; // available in your starter
import { Button } from '../../components/ui/button';
import { Input } from '../../components/ui/input';
import { Label } from '../../components/ui/label';
import InputError from '../../components/InputError.vue';
import { LoaderCircle } from 'lucide-vue-next';

// Props coming from Blade mount
interface Option { id: number; name: string }

// Props are provided by the server when calling Inertia::render('forms/CreateUser', props)
const props = defineProps<{
  languages: Option[];
  interests: Option[];
  identityTypes: Option[];
  postUrl: string; // e.g. route('users.store')
}>();

// Optional: keep it as a dialog-style page (button toggles form)
const open = ref(false);

// Inertia form fields aligned with StoreNewUserPostRequest
const form = useInertiaForm({
  name: '',
  surname: '',
  email: '',
  mobile: '',
  birth_date: '',
  language_id: null as number | null,
  identity_type_id: null as number | null,
  id_number: '',
  interests: [] as number[],
});

const submitting = computed(() => form.processing);

function submit() {
  form.post(props.postUrl, {
    onSuccess: () => {
      form.reset('id_number', 'interests');
      open.value = false;
    },
    preserveScroll: true,
  });
}
</script>

<template>
  <Head title="Create User" />

  <div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Create User</h1>
    <Button @click="open = true">Add User</Button>
  </div>

  <!-- Dialog (page-local) -->
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl dark:bg-zinc-900">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Create New User</h2>
        <button class="text-sm text-muted-foreground hover:underline" @click="open = false">Close</button>
      </div>

      <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submit">
        <div class="col-span-1">
          <Label for="name">Name</Label>
          <Input id="name" name="name" v-model="form.name" autocomplete="off" />
          <InputError :message="form.errors.name" />
        </div>

        <div class="col-span-1">
          <Label for="surname">Surname</Label>
          <Input id="surname" name="surname" v-model="form.surname" autocomplete="off" />
          <InputError :message="form.errors.surname" />
        </div>

        <div class="col-span-1">
          <Label for="email">Email</Label>
          <Input id="email" type="email" name="email" v-model="form.email" autocomplete="off" />
          <InputError :message="form.errors.email" />
        </div>

        <div class="col-span-1">
          <Label for="mobile">Mobile</Label>
          <Input id="mobile" name="mobile" v-model="form.mobile" autocomplete="off" />
          <InputError :message="form.errors.mobile" />
        </div>

        <div class="col-span-1">
          <Label for="birth_date">Birth Date</Label>
          <Input id="birth_date" type="date" name="birth_date" v-model="form.birth_date" />
          <InputError :message="form.errors.birth_date" />
        </div>

        <div class="col-span-1">
          <Label for="language_id">Language</Label>
          <select
            id="language_id"
            name="language_id"
            v-model="form.language_id"
            class="mt-2 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
          >
            <option :value="null">Select language</option>
            <option v-for="l in props.languages" :key="l.id" :value="l.id">{{ l.name }}</option>
          </select>
          <InputError :message="form.errors.language_id" />
        </div>

        <div class="col-span-1">
          <Label for="identity_type_id">Identity Type</Label>
          <select
            id="identity_type_id"
            name="identity_type_id"
            v-model="form.identity_type_id"
            class="mt-2 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
          >
            <option :value="null">Select identity type</option>
            <option v-for="t in props.identityTypes" :key="t.id" :value="t.id">{{ t.name }}</option>
          </select>
          <InputError :message="form.errors.identity_type_id" />
        </div>

        <div class="col-span-1">
          <Label for="id_number">ID Number</Label>
          <Input id="id_number" name="id_number" v-model="form.id_number" autocomplete="off" />
          <InputError :message="form.errors.id_number" />
        </div>

        <div class="col-span-1 md:col-span-2">
          <Label for="interests">Interests</Label>
          <select
            multiple
            id="interests"
            name="interests"
            v-model="form.interests"
            class="mt-2 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
            size="5"
          >
            <option v-for="i in props.interests" :key="i.id" :value="i.id">{{ i.name }}</option>
          </select>
          <InputError :message="form.errors.interests" />
        </div>

        <div class="col-span-1 md:col-span-2 mt-4 flex items-center justify-end gap-2">
          <Button type="button" variant="secondary" @click="open = false">Cancel</Button>
          <Button type="submit" :disabled="submitting">
            <LoaderCircle v-if="submitting" class="mr-2 h-4 w-4 animate-spin" />
            Create User
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>