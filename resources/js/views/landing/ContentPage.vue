<template>
  <div>
    <div v-if="loading" class="flex min-h-screen items-center justify-center bg-brand">
      <div class="h-10 w-10 animate-spin rounded-full border-2 border-gold/30 border-t-gold" />
    </div>

    <LandingShell v-else :settings="settings" :pages="pages">
      <div class="border-b border-surface-border bg-surface-muted/50">
        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
          <router-link to="/" class="text-sm font-medium text-accent hover:text-accent-dark">← Back to home</router-link>
          <h1 class="mt-4 text-3xl font-bold tracking-tight text-ink sm:text-4xl">{{ page.title }}</h1>
          <p v-if="page.excerpt" class="mt-3 text-base text-ink-muted">{{ page.excerpt }}</p>
          <p class="mt-4 text-xs text-ink-muted">
            Last updated {{ formatDate(page.updated_at) }}
          </p>
        </div>
      </div>

      <article class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <div v-if="error" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
          {{ error }}
        </div>
        <div v-else class="prose-landing space-y-4 text-ink-soft">
          <template v-for="(block, i) in contentBlocks" :key="i">
            <h2 v-if="block.type === 'h2'" class="pt-4 text-xl font-bold text-ink">{{ block.text }}</h2>
            <h3 v-else-if="block.type === 'h3'" class="pt-2 text-lg font-semibold text-ink">{{ block.text }}</h3>
            <ul v-else-if="block.type === 'ul'" class="list-disc space-y-1 pl-5">
              <li v-for="(li, j) in block.items" :key="j">{{ li }}</li>
            </ul>
            <p v-else class="leading-relaxed">{{ block.text }}</p>
          </template>
        </div>

        <div class="mt-12 flex flex-wrap gap-3 border-t border-surface-border pt-8">
          <router-link to="/pages/privacy" class="text-sm font-medium text-accent hover:underline">Privacy Policy</router-link>
          <span class="text-ink-muted">·</span>
          <router-link to="/pages/terms" class="text-sm font-medium text-accent hover:underline">Terms of Service</router-link>
          <span class="text-ink-muted">·</span>
          <router-link to="/#contact" class="text-sm font-medium text-accent hover:underline">Contact</router-link>
        </div>
      </article>
    </LandingShell>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import LandingShell from '@/components/LandingShell.vue';

const route = useRoute();
const loading = ref(true);
const error = ref('');
const page = ref({});
const settings = ref({});
const pages = ref([]);

const contentBlocks = computed(() => parseContent(page.value.content || ''));

function formatDate(value) {
  if (!value) return '—';
  try {
    return new Date(value).toLocaleDateString(undefined, {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
  } catch {
    return '—';
  }
}

function parseContent(raw) {
  const lines = String(raw).replace(/\r\n/g, '\n').split('\n');
  const blocks = [];
  let paragraph = [];
  let listItems = [];

  const flushParagraph = () => {
    if (paragraph.length) {
      blocks.push({ type: 'p', text: paragraph.join(' ').trim() });
      paragraph = [];
    }
  };
  const flushList = () => {
    if (listItems.length) {
      blocks.push({ type: 'ul', items: [...listItems] });
      listItems = [];
    }
  };

  for (const line of lines) {
    const trimmed = line.trim();
    if (!trimmed) {
      flushParagraph();
      flushList();
      continue;
    }
    if (trimmed.startsWith('## ')) {
      flushParagraph();
      flushList();
      blocks.push({ type: 'h2', text: trimmed.slice(3).trim() });
      continue;
    }
    if (trimmed.startsWith('### ')) {
      flushParagraph();
      flushList();
      blocks.push({ type: 'h3', text: trimmed.slice(4).trim() });
      continue;
    }
    if (trimmed.startsWith('- ') || trimmed.startsWith('* ')) {
      flushParagraph();
      listItems.push(trimmed.slice(2).trim());
      continue;
    }
    // Treat ALL CAPS short lines or lines ending with colon as headings when alone
    if (/^[A-Z][A-Z0-9 &/'-]{3,}$/.test(trimmed) && trimmed.length < 80) {
      flushParagraph();
      flushList();
      blocks.push({ type: 'h2', text: trimmed });
      continue;
    }
    flushList();
    paragraph.push(trimmed);
  }
  flushParagraph();
  flushList();
  return blocks;
}

async function load() {
  loading.value = true;
  error.value = '';
  try {
    const slug = route.params.slug;
    const [pageRes, landingRes] = await Promise.all([
      axios.get(`/landing/pages/${slug}`),
      axios.get('/landing'),
    ]);
    page.value = pageRes.data.page || pageRes.data;
    settings.value = landingRes.data.settings || {};
    pages.value = landingRes.data.pages || landingRes.data.footer_pages || [];
  } catch (err) {
    error.value = err.response?.status === 404
      ? 'This page was not found or is not published.'
      : (err.response?.data?.message || 'Failed to load page.');
    page.value = { title: 'Page not found', content: '' };
    try {
      const { data } = await axios.get('/landing');
      settings.value = data.settings || {};
      pages.value = data.pages || data.footer_pages || [];
    } catch (_) {}
  } finally {
    loading.value = false;
  }
}

watch(() => route.params.slug, load);
onMounted(load);
</script>
