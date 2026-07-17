<template>
  <div class="flex h-screen bg-surface">
    <!-- Sidebar -->
    <aside class="w-64 bg-brand flex flex-col shadow-soft">
      <!-- Logo -->
      <div class="p-5 border-b border-white/10">
        <AppLogo theme="brand" size="md" tagline="HR & Payroll Platform" link-to="/dashboard" />
      </div>
      
      <!-- Navigation -->
      <nav class="flex-1 overflow-y-auto p-4">
        <!-- Loop through menu sections -->
        <div v-for="(section, sectionIndex) in menuSections" :key="sectionIndex" class="mb-6 last:mb-0">
          <!-- Section Header -->
          <div class="px-2 mb-3 flex items-center gap-2">
            <h3 class="text-xs font-bold text-white/40 uppercase tracking-wider">{{ section.title }}</h3>
            <div class="flex-1 h-px bg-white/10"></div>
          </div>
          
          <!-- Section Items -->
          <div class="space-y-0.5">
            <router-link
              v-for="item in section.items"
              :key="item.path"
              :to="item.path"
              class="flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group relative overflow-hidden"
              :class="{
                'bg-accent text-white shadow-sm': isActive(item.path),
                'text-white/70 hover:bg-white/10 hover:text-white': !isActive(item.path)
              }"
            >
              <div class="flex items-center space-x-3">
            <!-- Dashboard Icon -->
            <svg v-if="item.name === 'dashboard'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
            </svg>
            <!-- Employees Icon -->
            <svg v-else-if="item.name === 'employees'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
            </svg>
            <!-- Attendance Icon -->
            <svg v-else-if="item.name === 'attendance'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
            </svg>
            <!-- Leaves Icon -->
            <svg v-else-if="item.name === 'leaves'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            <!-- Leave Settings Icon -->
            <svg v-else-if="item.name === 'leave-settings'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H7v-2h5v2zm5-4H7v-2h10v2zm0-4H7V7h10v2z"/>
            </svg>
            <!-- Payroll Icon -->
            <svg v-else-if="item.name === 'payroll'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
            </svg>
            <!-- Salary Components Icon -->
            <svg v-else-if="item.name === 'salary-components'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/>
            </svg>
            <!-- Loans Icon -->
            <svg v-else-if="item.name === 'loans'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
            </svg>
            <!-- Salary Advances Icon -->
            <svg v-else-if="item.name === 'salary-advances'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M15 1H9v2h6V1zm-4 13h2V8h-2v6zm8.03-6.61l1.42-1.42c-.43-.51-.9-.99-1.41-1.41l-1.42 1.42C16.07 4.74 14.12 4 12 4c-4.97 0-9 4.03-9 9s4.02 9 9 9 9-4.03 9-9c0-2.12-.74-4.07-1.97-5.61zM12 20c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
            </svg>
            <!-- CV Bank Icon -->
            <svg v-else-if="item.name === 'cvs'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
            </svg>
            <!-- Deployments Icon -->
            <svg v-else-if="item.name === 'deployments'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
            </svg>
            <!-- Departments Icon -->
            <svg v-else-if="item.name === 'departments'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
            </svg>
            <!-- Timesheets Icon -->
            <svg v-else-if="item.name === 'timesheets'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
            </svg>
            <!-- Onboarding Icon -->
            <svg v-else-if="item.name === 'onboarding'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            <!-- Training Icon -->
            <svg v-else-if="item.name === 'training'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
            </svg>
            <!-- Travel Icon -->
            <svg v-else-if="item.name === 'travel'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
            </svg>
            <!-- Shifts Icon -->
            <svg v-else-if="item.name === 'shifts'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-.22-13h-.06c-.4 0-.72.32-.72.72v4.72c0 .35.18.68.49.86l4.15 2.49c.34.2.78.1.98-.24.21-.34.1-.79-.25-.99l-3.87-2.3V7.72c0-.4-.32-.72-.72-.72z"/>
            </svg>
            <!-- Helpdesk Icon -->
            <svg v-else-if="item.name === 'helpdesk'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm0-4h-2V6h2v4z"/>
            </svg>
            <!-- Files Icon -->
            <svg v-else-if="item.name === 'files'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
            </svg>
            <!-- Calendar Icon -->
            <svg v-else-if="item.name === 'calendar'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M20 3h-1V1h-2v2H7V1H5v2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 18H4V8h16v13z"/>
            </svg>
            <!-- Organization Icon -->
            <svg v-else-if="item.name === 'organization'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
            </svg>
            <!-- Profile Icon -->
            <svg v-else-if="item.name === 'profile'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
            <!-- Recruitment Icon -->
            <svg v-else-if="item.name === 'recruitment'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
            </svg>
            <!-- Performance Icon -->
            <svg v-else-if="item.name === 'performance'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/>
            </svg>
            <!-- Assets Icon -->
            <svg v-else-if="item.name === 'assets'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>
            </svg>
            <!-- Announcements Icon -->
            <svg v-else-if="item.name === 'announcements'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M18 11v2h4v-2h-4zm-2 6.61c.96.71 2.21 1.65 3.2 2.39.4-.61.75-1.26 1.06-1.96-.98-.74-2.23-1.68-3.2-2.4-.37.66-.71 1.32-1.06 1.97zM20.26 5.96c-.31-.7-.66-1.35-1.06-1.96-.99.74-2.24 1.68-3.2 2.4.35.65.69 1.31 1.06 1.96.97-.72 2.22-1.67 3.2-2.4zM12 8H4v8h8c1.1 0 2-.9 2-2v-4c0-1.1-.9-2-2-2zm-2 6H6v-4h4v4z"/>
            </svg>
            <!-- Overtime Icon -->
            <svg v-else-if="item.name === 'overtime'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
            </svg>
            <!-- Designations Icon -->
            <svg v-else-if="item.name === 'designations'" class="w-5 h-5" :class="isActive(item.path) ? 'text-white' : 'text-white/60'" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
                <span>{{ item.label }}</span>
              </div>
              <!-- Badge for active item -->
              <span v-if="isActive(item.path)" class="w-2 h-2 bg-white rounded-full"></span>
            </router-link>
          </div>
        </div>
      </nav>
      
      <!-- User Section & Logout -->
      <div class="px-4 pb-4 pt-2 border-t border-white/10">
        <!-- User Info -->
        <div class="px-4 py-3 mb-2 bg-white/5 rounded-xl ring-1 ring-white/10">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-accent rounded-full flex items-center justify-center flex-shrink-0">
              <span class="text-white text-sm font-bold">{{ userInitials }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-white truncate">{{ user?.name }}</p>
              <p class="text-xs text-white/50 capitalize font-medium">{{ user?.role }}</p>
            </div>
          </div>
        </div>
        
        <!-- Logout Button -->
        <button 
          @click="handleLogout" 
          class="flex items-center justify-center w-full px-4 py-2.5 text-white/70 hover:bg-accent/20 hover:text-white border border-white/10 hover:border-accent/40 rounded-xl transition-all duration-200 text-sm font-medium group"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <header class="bg-brand sticky top-0 z-10 shadow-soft">
        <div class="flex items-center justify-between px-6 py-4">
          <h2 class="text-2xl font-bold text-white">{{ pageTitle }}</h2>
          
          <div class="flex items-center space-x-4">
            <ThemeToggle variant="header" />

            <!-- Notifications -->
            <div class="relative">
              <button 
                @click="showNotifications = !showNotifications" 
                class="relative p-2 text-gold hover:bg-white/10 rounded-xl transition-colors"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-accent rounded-full">
                  {{ unreadCount > 9 ? '9+' : unreadCount }}
                </span>
              </button>
              
              <!-- Notifications Dropdown -->
              <div v-if="showNotifications" class="absolute right-0 mt-2 w-[22rem] overflow-hidden rounded-2xl border border-surface-border bg-surface-card shadow-soft z-50">
                <div class="flex items-center justify-between border-b border-surface-border bg-surface-muted px-4 py-3">
                  <div>
                    <h3 class="text-sm font-bold text-ink">Notifications</h3>
                    <p class="text-[11px] text-ink-muted">{{ unreadCount }} unread</p>
                  </div>
                  <button
                    type="button"
                    class="text-xs font-semibold text-accent hover:text-accent-dark disabled:opacity-40"
                    :disabled="!unreadCount"
                    @click="markAllAsRead"
                  >
                    Mark all read
                  </button>
                </div>
                <div class="max-h-80 overflow-y-auto">
                  <div v-if="notifications.length === 0" class="px-6 py-10 text-center">
                    <p class="text-sm font-medium text-ink">You're all caught up</p>
                    <p class="mt-1 text-xs text-ink-muted">No unread notifications</p>
                  </div>
                  <button
                    v-for="notification in notifications.slice(0, 8)"
                    :key="notification.id"
                    type="button"
                    class="flex w-full gap-3 border-b border-surface-border px-4 py-3 text-left transition hover:bg-surface-muted"
                    :class="{ 'bg-accent-soft/40': !notification.is_read }"
                    @click="handleNotificationClick(notification)"
                  >
                    <span
                      class="mt-1.5 h-2 w-2 shrink-0 rounded-full"
                      :class="notification.is_read ? 'bg-transparent' : 'bg-accent'"
                    />
                    <span class="min-w-0 flex-1">
                      <span class="flex items-start justify-between gap-2">
                        <span class="text-sm font-semibold text-ink" :class="{ 'font-bold': !notification.is_read }">
                          {{ notification.title }}
                        </span>
                        <span class="shrink-0 text-[11px] text-ink-muted">{{ formatDate(notification.created_at) }}</span>
                      </span>
                      <span class="mt-0.5 line-clamp-2 text-xs text-ink-soft">{{ notification.message }}</span>
                    </span>
                  </button>
                </div>
                <div class="border-t border-surface-border bg-surface-muted p-3">
                  <router-link
                    to="/notifications"
                    class="block rounded-lg py-2 text-center text-sm font-semibold text-accent transition hover:bg-white hover:text-accent-dark"
                    @click="showNotifications = false"
                  >
                    View all notifications
                  </router-link>
                </div>
              </div>
            </div>

            <div class="hidden sm:flex items-center gap-3 pl-2">
              <div class="w-9 h-9 bg-accent rounded-full flex items-center justify-center">
                <span class="text-white text-xs font-bold">{{ userInitials }}</span>
              </div>
              <span class="text-sm font-medium text-white/90">{{ user?.name }}</span>
            </div>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="flex-1 overflow-auto bg-surface">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { usePermissionStore } from '@/stores/permission';
import ThemeToggle from '@/components/ThemeToggle.vue';
import AppLogo from '@/components/AppLogo.vue';
import { HRMS_LOGIN_PATH } from '@/config/authPaths';
import { resolveNotificationTarget } from '@/utils/notificationTarget';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const permissionStore = usePermissionStore();

const user = computed(() => authStore.user);
const userInitials = computed(() => {
  if (!user.value?.name) return '??';
  const names = user.value.name.split(' ');
  return names.length > 1 
    ? `${names[0][0]}${names[1][0]}`.toUpperCase()
    : names[0].substring(0, 2).toUpperCase();
});


const isActive = (path) => {
  if (path === '/dashboard') return route.path === '/dashboard';
  return route.path === path || route.path.startsWith(path + '/');
};

const showNotifications = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);

const pageTitle = computed(() => {
  const titles = {
    '/dashboard': 'Dashboard',
    '/employees': 'Employees',
    '/attendance': 'Attendance',
    '/leaves': 'Leave Management',
    '/leave-settings': 'Leave Settings',
    '/payroll': 'Payroll',
    '/salary-components': 'Salary Components',
    '/loans': 'Loans',
    '/salary-advances': 'Salary Advances',
    '/cvs': 'CV Bank',
    '/deployments': 'Deployments',
    '/departments': 'Departments',
    '/designations': 'Designations',
    '/timesheets': 'Timesheets',
    '/onboarding': 'Onboarding',
    '/recruitment': 'Recruitment',
    '/performance': 'Performance',
    '/assets': 'Assets',
    '/announcements': 'Announcements',
    '/overtime': 'Overtime',
    '/training': 'Training',
    '/travel-expenses': 'Travel & Expenses',
    '/shifts': 'Shift Scheduling',
    '/shifts/my': 'My Schedule',
    '/shifts/rosters': 'Shift Rosters',
    '/helpdesk': 'Helpdesk',
    '/files': 'Files',
    '/calendar': 'Calendar',
    '/organization': 'Organization',
    '/profile': 'My Profile',
    '/notifications': 'Notifications',
    '/admin/roles': 'Roles & Permissions',
    '/admin/user-roles': 'User Role Management',
  };
  
  const path = route.path;
  if (path === '/dashboard') return titles['/dashboard'];
  const match = Object.entries(titles)
    .filter(([key]) => key !== '/' && (path === key || path.startsWith(key + '/')))
    .sort((a, b) => b[0].length - a[0].length)[0];
  return match ? match[1] : 'Payroll Digital';
});

  const menuItems = computed(() => {
  const allMenuItems = [
    { name: 'dashboard', path: '/dashboard', label: 'Dashboard', icon: '📊', module: 'dashboard' },
    { name: 'employees', path: '/employees', label: 'Employees', icon: '👥', module: 'employees' },
    { name: 'attendance', path: '/attendance', label: 'Attendance', icon: '📅', module: 'attendance' },
    { name: 'overtime', path: '/overtime', label: 'Overtime', icon: '⏰', module: 'overtime' },
    { name: 'leaves', path: '/leaves', label: 'Leave Requests', icon: '🏖️', module: 'leaves' },
    { name: 'leave-settings', path: '/leave-settings', label: 'Leave Settings', icon: '📋', module: 'leaves', permissions: ['leaves.manage'] },
    { name: 'payroll', path: '/payroll', label: 'Payroll', icon: '💰', module: 'payroll' },
    { name: 'salary-components', path: '/salary-components', label: 'Salary Components', icon: '💼', module: 'salary_components', permissions: ['salary_components.view', 'salary_components.manage'] },
    { name: 'loans', path: '/loans', label: 'Loans', icon: '💵', module: 'loans' },
    { name: 'salary-advances', path: '/salary-advances', label: 'Salary Advances', icon: '💸', module: 'salary_advances' },
    { name: 'recruitment', path: '/recruitment', label: 'Recruitment', icon: '🧑‍💼', module: 'recruitment' },
    { name: 'cvs', path: '/cvs', label: 'CV Bank', icon: '📄', module: 'cv_bank' },
    { name: 'deployments', path: '/deployments', label: 'Deployments', icon: '🌍', module: 'deployments' },
    { name: 'departments', path: '/departments', label: 'Departments', icon: '🏢', module: 'departments' },
    { name: 'designations', path: '/designations', label: 'Designations', icon: '🏷️', module: 'departments', permissions: ['departments.view', 'departments.manage'] },
    { name: 'timesheets', path: '/timesheets', label: 'Timesheets', icon: '⏱️', module: 'timesheets' },
    { name: 'onboarding', path: '/onboarding', label: 'Onboarding', icon: '🎯', module: 'onboarding' },
    { name: 'performance', path: '/performance', label: 'Performance', icon: '📈', module: 'performance' },
    { name: 'training', path: '/training', label: 'Training', icon: '📚', module: 'training' },
    { name: 'travel', path: '/travel-expenses', label: 'Travel & Expenses', icon: '✈️', module: 'travel' },
    { name: 'shifts', path: '/shifts', label: 'Shift Scheduling', icon: '🕐', module: 'shifts' },
    { name: 'assets', path: '/assets', label: 'Assets', icon: '💼', module: 'assets' },
    { name: 'announcements', path: '/announcements', label: 'Announcements', icon: '📢', module: 'announcements' },
    { name: 'helpdesk', path: '/helpdesk', label: 'Helpdesk', icon: '🎫', module: 'helpdesk' },
    { name: 'files', path: '/files', label: 'Files', icon: '📁', module: 'files' },
    { name: 'calendar', path: '/calendar', label: 'Calendar', icon: '📆', module: 'calendar' },
    { name: 'organization', path: '/organization', label: 'Organization', icon: '🏛️', module: 'organization' },
    { name: 'profile', path: '/profile', label: 'My Profile', icon: '👤', module: 'employees' },
    { name: 'roles', path: '/admin/roles', label: 'Roles & Permissions', icon: '🔐', module: 'roles' },
    { name: 'user-roles', path: '/admin/user-roles', label: 'User Role Management', icon: '👥', module: 'users' },
  ];

  return allMenuItems.filter((item) => {
    if (['dashboard', 'profile', 'announcements'].includes(item.name)) {
      return true;
    }

    if (!permissionStore.loaded) {
      return false;
    }

    if (!permissionStore.canAccessModule(item.module)) {
      return false;
    }

    if (item.permissions?.length) {
      return permissionStore.hasAnyPermission(item.permissions);
    }

    return true;
  }).map((item) => {
    // Employees (view-only) land on personal schedule; managers/HR land on management
    if (item.name === 'shifts') {
      const canManageShifts = permissionStore.hasAnyPermission([
        'shifts.assign', 'shifts.manage', 'shifts.create', 'shifts.update', 'shifts.delete',
      ]);
      return { ...item, path: canManageShifts ? '/shifts' : '/shifts/my' };
    }
    return item;
  });
});

const menuSections = computed(() => {
  const allItems = menuItems.value;
  
  // Define sections with their respective item names
  const sections = [
    {
      title: 'Main',
      items: allItems.filter(item => ['dashboard', 'profile'].includes(item.name))
    },
    {
      title: 'Workforce',
      items: allItems.filter(item => ['employees', 'attendance', 'overtime', 'leaves', 'leave-settings', 'timesheets', 'shifts'].includes(item.name))
    },
    {
      title: 'Compensation',
      items: allItems.filter(item => ['payroll', 'salary-components', 'loans', 'salary-advances'].includes(item.name))
    },
    {
      title: 'Recruitment',
      items: allItems.filter(item => ['recruitment', 'cvs', 'deployments', 'onboarding'].includes(item.name))
    },
    {
      title: 'Organization',
      items: allItems.filter(item => ['departments', 'designations', 'organization'].includes(item.name))
    },
    {
      title: 'Development',
      items: allItems.filter(item => ['performance', 'training', 'travel'].includes(item.name))
    },
    {
      title: 'Resources',
      items: allItems.filter(item => ['assets', 'announcements', 'helpdesk', 'files', 'calendar'].includes(item.name))
    },
    {
      title: 'Administration',
      items: allItems.filter(item => ['roles', 'user-roles'].includes(item.name))
    }
  ];
  
  // Filter out empty sections
  return sections.filter(section => section.items.length > 0);
});

const fetchNotifications = async () => {
  try {
    const response = await axios.get('/notifications?is_read=false');
    notifications.value = response.data.data || response.data;
    const countResponse = await axios.get('/notifications/unread-count');
    unreadCount.value = countResponse.data.unread_count ?? countResponse.data.count ?? 0;
  } catch (error) {
    console.error('Failed to fetch notifications:', error);
  }
};

const handleNotificationClick = async (notification) => {
  try {
    await axios.post(`/notifications/${notification.id}/mark-read`);
    notification.is_read = true;
    unreadCount.value = Math.max(0, unreadCount.value - 1);
    showNotifications.value = false;

    const target = resolveNotificationTarget(notification);
    if (!target) return;

    if (/^https?:\/\//i.test(target)) {
      window.location.href = target;
      return;
    }

    router.push(target);
  } catch (error) {
    console.error('Failed to mark notification as read:', error);
  }
};

const markAllAsRead = async () => {
  try {
    await axios.post('/notifications/mark-all-read');
    notifications.value.forEach(n => n.is_read = true);
    unreadCount.value = 0;
  } catch (error) {
    console.error('Failed to mark all as read:', error);
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diff = now - date;
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);
  
  if (minutes < 1) return 'Just now';
  if (minutes < 60) return `${minutes}m ago`;
  if (hours < 24) return `${hours}h ago`;
  if (days < 7) return `${days}d ago`;
  return date.toLocaleDateString();
};

const handleLogout = async () => {
  await authStore.logout();
  router.push(HRMS_LOGIN_PATH);
};

onMounted(async () => {
  // Ensure permissions are loaded
  if (permissionStore.permissions.length === 0 && !permissionStore.isSuperAdmin) {
    try {
      await permissionStore.fetchMyPermissions();
    } catch (error) {
      console.error('Failed to fetch permissions in DashboardLayout:', error);
    }
  }
  
  fetchNotifications();
  // Refresh notifications every 30 seconds
  setInterval(fetchNotifications, 30000);
});
</script>
