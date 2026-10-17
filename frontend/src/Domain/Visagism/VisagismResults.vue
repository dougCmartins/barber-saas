<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import type { HaircutSuggestion, VisagismResult } from '@/Domain/Visagism/visagismModel'
import { buttonClasses } from '@/ui/buttonClasses'
import { cn } from '@/ui/cn'

const props = defineProps<{
  result: VisagismResult
  selectedSuggestionId: number | null
}>()

const emit = defineEmits<{
  select: [suggestion: HaircutSuggestion]
  choose: []
  override: []
}>()

const { t } = useI18n()

const selected = computed(
  () => props.result.suggestions.find((item) => item.id === props.selectedSuggestionId) ?? null,
)
</script>

<template>
  <section class="flex flex-1 flex-col gap-4">
    <header class="pt-2">
      <p class="text-xs font-medium uppercase tracking-[0.14em] text-primary">
        {{ t('visagism.resultsKicker') }}
      </p>
      <h1 class="mt-1 font-display text-2xl font-semibold uppercase tracking-wide text-foreground">
        {{ t('visagism.resultsTitle') }}
      </h1>
    </header>

    <article class="rounded-lg border border-primary/20 bg-card p-4">
      <p class="text-xs uppercase tracking-wide text-muted-foreground">{{ t('visagism.faceShapeLabel') }}</p>
      <h2 class="mt-1 font-display text-xl font-semibold text-foreground">
        {{
          t('visagism.faceShapeValue', {
            shape: t(`visagism.faceShapes.${result.face_shape}`, result.face_shape),
          })
        }}
      </h2>
      <p class="mt-2 text-sm leading-6 text-muted-foreground">{{ result.face_geometry_summary }}</p>
      <div class="mt-3 flex flex-wrap gap-2">
        <span class="rounded-md border border-border bg-muted/60 px-2.5 py-1 text-xs text-foreground">
          {{ t(`visagism.hairTypes.${result.hair_type}`, result.hair_type) }}
        </span>
      </div>
    </article>

    <div class="-mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-1">
      <article
        v-for="suggestion in result.suggestions"
        :key="suggestion.id"
        :class="
          cn(
            'flex min-w-[85%] snap-center flex-col overflow-hidden rounded-lg border bg-card',
            suggestion.id === selectedSuggestionId ? 'border-primary/50' : 'border-border',
          )
        "
      >
        <div class="relative aspect-[4/3] bg-muted">
          <img
            v-if="suggestion.image_url"
            :src="suggestion.image_url"
            :alt="suggestion.name"
            class="size-full object-cover"
            loading="lazy"
          />
          <div
            v-else
            class="flex size-full items-end bg-gradient-to-br from-muted via-card to-primary/15 p-4"
          >
            <span class="font-display text-lg font-semibold text-foreground/80">{{ suggestion.name }}</span>
          </div>
          <span
            class="absolute right-3 top-3 rounded-md bg-primary px-2.5 py-1 text-xs font-semibold text-primary-foreground"
          >
            {{ t('visagism.matchBadge', { percent: suggestion.match_percentage }) }}
          </span>
        </div>
        <div class="flex flex-1 flex-col gap-3 p-4">
          <h3 class="font-display text-lg font-semibold text-foreground">{{ suggestion.name }}</h3>
          <p class="text-sm leading-6 text-muted-foreground">{{ suggestion.justification }}</p>
          <button
            type="button"
            :class="
              buttonClasses(
                suggestion.id === selectedSuggestionId ? 'default' : 'outline',
                'default',
                'mt-auto w-full',
              )
            "
            @click="emit('select', suggestion)"
          >
            {{
              suggestion.id === selectedSuggestionId
                ? t('visagism.selectedStyle')
                : t('visagism.selectStyle')
            }}
          </button>
        </div>
      </article>
    </div>

    <button
      type="button"
      :class="buttonClasses('default', 'lg', 'mt-auto h-12 w-full')"
      :disabled="!selected"
      @click="emit('choose')"
    >
      {{ t('visagism.chooseStyle') }}
    </button>

    <button
      type="button"
      class="text-center text-sm text-primary underline-offset-4 hover:underline"
      @click="emit('override')"
    >
      {{ t('visagism.overrideLink') }}
    </button>
  </section>
</template>
