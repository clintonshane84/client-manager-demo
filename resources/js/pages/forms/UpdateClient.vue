<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head, useForm as useInertiaForm, router, Link } from '@inertiajs/vue3'
import { Button } from '../../components/ui/button'
import { Input } from '../../components/ui/input'
import { Label } from '../../components/ui/label'
import InputError from '../../components/InputError.vue'
import { LoaderCircle } from 'lucide-vue-next'

type Option = { id: number; name: string }
type Language = Option
type Interest = Option

type ClientPayload = {
  id: number
  name: string
  surname: string
  email: string
  mobile: string
  birth_date?: string | null
  language?: Language | null
  language_id?: number | null
  interests?: Interest[]
  identity?: { identity_type_id?: number | null } | null
}

const props = defineProps<{
  client: ClientPayload
  languages: Option[]
  interests: Option[]
  identityTypes: Option[]
  putUrl: string // e.g. route('clients.update', client.id)
}>()

// CSRF token (from Blade layout <meta name="csrf-token" ...>)
const csrf = ref<string>('')

onMounted(async () => {
  const meta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null
  csrf.value = meta?.content ?? ''
  try { await fetch('/sanctum/csrf-cookie', { credentials: 'include' }) } catch {}
})

// Prefill form with client data
const initialInterests = (props.client.interests ?? []).map(i => i.id)
const initialLanguageId =
  props.client.language?.id ?? (props.client.language_id ?? null)
const initialIdentityTypeId = props.client.identity?.identity_type_id ?? null

const form = useInertiaForm({
  name: props.client.name ?? '',
  surname: props.client.surname ?? '',
  email: props.client.email ?? '',
  mobile: props.client.mobile ?? '',
  birth_date: props.client.birth_date ?? '',
  language_id: initialLanguageId as number | null,
  identity_type_id: initialIdentityTypeId as number | null,
  // ID number is intentionally blank (don’t show encrypted value)
  id_number: '' as string,
  interests: initialInterests as number[],
  _token: '' as string,
})

const submitting = computed(() => form.processing)

function submit() {
  // Attach token + avoid sending empty id_number (prevents accidental clearing)
  form.transform((data) => {
    const payload: Record<string, any> = { ...data, _token: csrf.value }
    if (!payload.id_number) delete payload.id_number
    return payload
  })

  form.put(props.putUrl, {
    onSuccess: () => {
      router.visit('/clients') // back to list after 303 redirect
    },
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Edit Client" />

  <div class="mx-auto max-w-2xl p-4 md:p-8">
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-xl font-semibold">Edit Client</h1>

      <Link href="/clients" method="get" as="button">
        <Button variant="secondary">Back to Clients</Button>
      </Link>
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
        <Input id="id_number" name="id_number" v-model="form.id_number" autocomplete="off" placeholder="Leave blank to keep unchanged" />
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
        <Button type="submit" :disabled="submitting">
          <LoaderCircle v-if="submitting" class="mr-2 h-4 w-4 animate-spin" />
          Save Changes
        </Button>
      </div>
    </form>
  </div>
</template>
