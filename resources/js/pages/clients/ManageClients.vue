<!-- resources/js/pages/clients/Index.vue -->
<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

// Use relative paths from /pages/clients to your shared UI components
import { Button } from '../../components/ui/button';

type Interest = { id: number; name: string }
type Language = { id: number; name: string }

type UserRow = {
  id: number
  name: string
  surname: string
  email: string
  mobile: string
  birth_date?: string | null
  language?: Language | null
  interests?: Interest[]
}

const props = defineProps<{
  users: UserRow[]   // provided by your controller: Inertia::render('clients/Index', ['users' => $users])
}>();

const deletingId = ref<number | null>(null);
const hasNoUsers = computed(() => (props.users?.length ?? 0) === 0);

function fullName(u: UserRow) {
  return `${u.name} ${u.surname}`.trim();
}

function interestSummary(u: UserRow) {
  const count = u.interests?.length ?? 0;
  return count === 0 ? '—' : `${count} selected`;
}

function handleDelete(id: number) {
  if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
  deletingId.value = id;
  router.delete(`/users/${id}`, {
    preserveScroll: true,
    onFinish: () => (deletingId.value = null),
  });
}
</script>

<template>
  <Head title="Clients" />

  <div class="mx-auto max-w-6xl p-4 md:p-8">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight">Clients</h1>
        <p class="text-sm text-muted-foreground">Manage your client records</p>
      </div>

      <!-- Primary Create button -> GET /user/create -->
      <Link href="/user/create" method="get" as="button">
        <Button class="whitespace-nowrap">
          + Create Client
        </Button>
      </Link>
    </div>

    <!-- Empty state -->
    <div v-if="hasNoUsers" class="rounded-lg border border-dashed p-10 text-center">
      <p class="mb-4 text-muted-foreground">No clients yet.</p>
      <Link href="/user/create" method="get" as="button">
        <Button> Add your first client </Button>
      </Link>
    </div>

    <!-- List table -->
    <div v-else class="overflow-x-auto rounded-lg border">
      <table class="w-full text-left text-sm">
        <thead class="bg-muted/50">
          <tr>
            <th class="px-4 py-3 font-medium">Name</th>
            <th class="px-4 py-3 font-medium">Email</th>
            <th class="px-4 py-3 font-medium">Mobile</th>
            <th class="px-4 py-3 font-medium">Language</th>
            <th class="px-4 py-3 font-medium">Interests</th>
            <th class="px-4 py-3 font-medium text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="u in props.users"
            :key="u.id"
            class="border-t"
          >
            <td class="px-4 py-3 font-medium">
              {{ fullName(u) }}
            </td>
            <td class="px-4 py-3">
              {{ u.email }}
            </td>
            <td class="px-4 py-3">
              {{ u.mobile }}
            </td>
            <td class="px-4 py-3">
              {{ u.language?.name ?? '—' }}
            </td>
            <td class="px-4 py-3">
              {{ interestSummary(u) }}
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center justify-end gap-2">
                <!-- Edit: adjust URI if your route differs -->
                <Link :href="`/users/${u.id}/edit`" method="get" as="button">
                  <Button variant="secondary" size="sm">Edit</Button>
                </Link>

                <Button
                  variant="destructive"
                  size="sm"
                  :disabled="deletingId === u.id"
                  @click="handleDelete(u.id)"
                >
                  <span v-if="deletingId === u.id">Deleting…</span>
                  <span v-else>Delete</span>
                </Button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
