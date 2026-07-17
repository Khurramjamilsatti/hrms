<template>
  <LandingShell :settings="settings" :pages="pages">
    <div class="border-b border-surface-border bg-surface-muted/50">
      <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-wider text-accent">
          {{ isDemo ? 'Book a Demo' : 'Contact' }}
        </p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-ink sm:text-4xl">
          {{ isDemo ? 'See Payroll Digital in action' : 'Talk with our team' }}
        </h1>
        <p class="mt-3 max-w-2xl text-base text-ink-muted">
          {{ isDemo
            ? 'Tell us a bit about your team and we will schedule a walkthrough of payroll, attendance, and HR workflows.'
            : 'Questions about rollout, pricing, or security? Send a message — we respond during business hours.' }}
        </p>
      </div>
    </div>

    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-5 lg:px-8 lg:py-16">
      <aside class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border border-surface-border bg-surface-card p-6 shadow-card">
          <h2 class="text-lg font-bold text-ink">Why teams reach out</h2>
          <ul class="mt-4 space-y-3 text-sm text-ink-soft">
            <li class="flex gap-2"><span class="text-accent">●</span> Payroll & compliance walkthrough</li>
            <li class="flex gap-2"><span class="text-accent">●</span> Leave & attendance workflows</li>
            <li class="flex gap-2"><span class="text-accent">●</span> Security & role-based access</li>
            <li class="flex gap-2"><span class="text-accent">●</span> Migration & onboarding support</li>
          </ul>
        </div>
        <div class="rounded-2xl bg-brand p-6 text-white shadow-soft">
          <p class="text-sm font-semibold text-gold">Direct contact</p>
          <p v-if="settings.contact_email" class="mt-3 text-sm text-white/80">
            <a :href="`mailto:${settings.contact_email}`" class="hover:text-white">{{ settings.contact_email }}</a>
          </p>
          <p v-if="settings.contact_phone" class="mt-2 text-sm text-white/80">{{ settings.contact_phone }}</p>
          <p class="mt-4 text-xs text-white/50">Protected form — spam submissions are filtered.</p>
        </div>
      </aside>

      <div class="lg:col-span-3">
        <form
          class="relative rounded-2xl border border-surface-border bg-surface-card p-6 shadow-card sm:p-8 space-y-5"
          @submit.prevent="submit"
        >
          <div v-if="success" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
            {{ success }}
          </div>
          <div v-if="error" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ error }}
          </div>

          <div class="flex gap-2 rounded-xl bg-surface-muted p-1">
            <button
              type="button"
              class="flex-1 rounded-lg px-3 py-2 text-sm font-semibold transition"
              :class="form.type === 'demo' ? 'bg-accent text-white' : 'text-ink-muted hover:text-ink'"
              @click="setType('demo')"
            >
              Book a Demo
            </button>
            <button
              type="button"
              class="flex-1 rounded-lg px-3 py-2 text-sm font-semibold transition"
              :class="form.type === 'contact' ? 'bg-accent text-white' : 'text-ink-muted hover:text-ink'"
              @click="setType('contact')"
            >
              Contact Us
            </button>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1.5 block text-sm font-semibold text-ink-soft">Full name *</label>
              <input v-model="form.name" type="text" required maxlength="120" class="field" autocomplete="name" />
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-semibold text-ink-soft">Work email *</label>
              <input v-model="form.email" type="email" required maxlength="255" class="field" autocomplete="email" />
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-semibold text-ink-soft">Phone</label>
              <input v-model="form.phone" type="tel" maxlength="40" class="field" autocomplete="tel" />
            </div>
            <div>
              <label class="mb-1.5 block text-sm font-semibold text-ink-soft">Company</label>
              <input v-model="form.company" type="text" maxlength="160" class="field" autocomplete="organization" />
            </div>
          </div>

          <div v-if="form.type === 'contact'">
            <label class="mb-1.5 block text-sm font-semibold text-ink-soft">Subject</label>
            <input v-model="form.subject" type="text" maxlength="180" class="field" />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-semibold text-ink-soft">Message *</label>
            <textarea v-model="form.message" rows="5" required maxlength="5000" class="field" :placeholder="messagePlaceholder" />
          </div>

          <!-- Honeypot -->
          <div class="absolute -left-[9999px] opacity-0" aria-hidden="true">
            <label>Website<input v-model="form.website" type="text" tabindex="-1" autocomplete="off" /></label>
          </div>

          <!-- Square “I’m human” challenge -->
          <div class="rounded-xl border border-surface-border bg-surface-muted/70 p-4">
            <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-ink-muted">Security check</p>
            <button
              type="button"
              class="flex w-full items-center gap-3 rounded-xl border bg-surface-card px-4 py-3 text-left transition"
              :class="squareVerified
                ? 'border-green-400 ring-1 ring-green-300'
                : 'border-surface-border hover:border-accent/50'"
              :disabled="squareLoading || squareVerified"
              @click="verifySquare"
            >
              <span
                class="flex h-7 w-7 shrink-0 items-center justify-center rounded border-2 transition"
                :class="squareVerified ? 'border-green-500 bg-green-500 text-white' : 'border-ink-muted'"
              >
                <svg v-if="squareVerified" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
              </span>
              <span class="text-sm font-medium text-ink">
                {{ squareVerified ? 'Verified — you’re human' : (squareLoading ? 'Verifying…' : 'Click the square to confirm you’re human') }}
              </span>
            </button>

            <div class="mt-4">
              <label class="mb-1.5 block text-sm font-semibold text-ink-soft">
                {{ challenge.question || 'Loading challenge…' }} *
              </label>
              <div class="flex gap-2">
                <input
                  v-model="form.challenge_answer"
                  type="text"
                  inputmode="numeric"
                  required
                  class="field max-w-[10rem]"
                  placeholder="Answer"
                  autocomplete="off"
                />
                <button type="button" class="rounded-xl border border-surface-border px-3 text-sm font-medium text-ink-muted hover:bg-surface-card" @click="loadChallenge">
                  Refresh
                </button>
              </div>
            </div>
          </div>

          <button
            type="submit"
            :disabled="submitting || !squareVerified"
            class="w-full rounded-xl bg-accent px-5 py-3 text-sm font-semibold text-white transition hover:bg-accent-dark disabled:opacity-50"
          >
            {{ submitting ? 'Sending…' : (form.type === 'demo' ? 'Request demo' : 'Send message') }}
          </button>
        </form>
      </div>
    </div>
  </LandingShell>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import LandingShell from '@/components/LandingShell.vue';

const route = useRoute();
const settings = ref({});
const pages = ref([]);
const challenge = ref({ token: '', question: '' });
const squareVerified = ref(false);
const squareLoading = ref(false);
const submitting = ref(false);
const success = ref('');
const error = ref('');

const form = reactive({
  type: route.query.intent === 'demo' ? 'demo' : 'contact',
  name: '',
  email: '',
  phone: '',
  company: '',
  subject: '',
  message: '',
  website: '',
  challenge_answer: '',
});

const isDemo = computed(() => form.type === 'demo');
const messagePlaceholder = computed(() =>
  form.type === 'demo'
    ? 'Team size, current tools, and a preferred time for a walkthrough…'
    : 'How can we help?'
);

function setType(type) {
  form.type = type;
  success.value = '';
  error.value = '';
}

async function loadShell() {
  try {
    const { data } = await axios.get('/landing');
    settings.value = data.settings || {};
    pages.value = data.pages || [];
  } catch (_) {}
}

async function loadChallenge() {
  squareVerified.value = false;
  form.challenge_answer = '';
  try {
    const { data } = await axios.get('/contact/challenge');
    challenge.value = data;
  } catch (_) {
    error.value = 'Could not load security challenge. Please refresh the page.';
  }
}

async function verifySquare() {
  if (!challenge.value.token || squareVerified.value) return;
  squareLoading.value = true;
  error.value = '';
  try {
    await axios.post('/contact/challenge/square', { token: challenge.value.token });
    squareVerified.value = true;
  } catch (err) {
    error.value = err.response?.data?.message || 'Security checkbox failed. Refresh and try again.';
    await loadChallenge();
  } finally {
    squareLoading.value = false;
  }
}

async function submit() {
  submitting.value = true;
  success.value = '';
  error.value = '';
  try {
    const { data } = await axios.post('/contact', {
      type: form.type,
      name: form.name,
      email: form.email,
      phone: form.phone || null,
      company: form.company || null,
      subject: form.subject || null,
      message: form.message,
      website: form.website,
      challenge_token: challenge.value.token,
      challenge_answer: form.challenge_answer,
      human_square: squareVerified.value ? 1 : 0,
    });
    success.value = data.message || 'Submitted successfully.';
    form.name = '';
    form.email = '';
    form.phone = '';
    form.company = '';
    form.subject = '';
    form.message = '';
    form.website = '';
    await loadChallenge();
  } catch (err) {
    error.value = err.response?.data?.message || 'Submission failed. Please try again.';
    if (err.response?.status === 422) {
      await loadChallenge();
    }
  } finally {
    submitting.value = false;
  }
}

onMounted(async () => {
  await Promise.all([loadShell(), loadChallenge()]);
});
</script>

<style scoped>
.field {
  @apply w-full rounded-xl border border-surface-border bg-surface-card px-4 py-2.5 text-ink focus:border-transparent focus:outline-none focus:ring-2 focus:ring-accent;
}
</style>
