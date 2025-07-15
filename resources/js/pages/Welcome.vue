<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome to Task Manager">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
        <header class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
            <nav class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="/logo.png" alt="App Logo" class="h-8 w-8" />
                    <span class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Task Manager</span>
                </div>
                <div class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </nav>
        </header>
        <div class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
            <main class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div
                    class="flex-1 rounded-br-lg rounded-bl-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <h1 class="mb-1 text-2xl font-bold lg:text-3xl">Transform your productivity, master your goals</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
                        A simple and powerful task management system to help you stay organized and productive.
                    </p>
                    <ul class="mb-4 flex flex-col lg:mb-6">
                        <li
                            class="relative flex items-center gap-4 py-2 before:absolute before:top-1/2 before:bottom-0 before:left-[0.4rem] before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A]"
                        >
                            <span class="relative bg-white py-1 dark:bg-[#161615]">
                                <span
                                    class="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-[#e3e3e0] bg-[#FDFDFC] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]" />
                                </span>
                            </span>
                            <span>
                                Create and organize tasks with ease
                            </span>
                        </li>
                        <li
                            class="relative flex items-center gap-4 py-2 before:absolute before:top-0 before:bottom-1/2 before:left-[0.4rem] before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A]"
                        >
                            <span class="relative bg-white py-1 dark:bg-[#161615]">
                                <span
                                    class="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-[#e3e3e0] bg-[#FDFDFC] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]" />
                                </span>
                            </span>
                            <span>
                                Track progress and set priorities
                            </span>
                        </li>
                        <li
                            class="relative flex items-center gap-4 py-2 before:absolute before:top-0 before:bottom-1/2 before:left-[0.4rem] before:border-l before:border-[#e3e3e0] dark:before:border-[#3E3E3A]"
                        >
                            <span class="relative bg-white py-1 dark:bg-[#161615]">
                                <span
                                    class="flex h-3.5 w-3.5 items-center justify-center rounded-full border border-[#e3e3e0] bg-[#FDFDFC] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]" />
                                </span>
                            </span>
                            <span>
                                Collaborate with your team
                            </span>
                        </li>
                    </ul>
                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            <Link
                                :href="route('register')"
                                class="inline-block rounded-sm border border-black bg-[#1b1b18] px-5 py-1.5 text-sm leading-normal text-white hover:border-black hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:border-white dark:hover:bg-white"
                            >
                                Get started
                            </Link>
                        </li>
                    </ul>
                </div>
                <div
                    class="relative -mb-px aspect-335/376 w-full shrink-0 overflow-hidden rounded-t-lg bg-[#f0f4ff] lg:mb-0 lg:-ml-px lg:aspect-auto lg:w-[438px] lg:rounded-t-none lg:rounded-r-lg dark:bg-[#0a1429]"
                >

                    <div class="flex h-full w-full items-center justify-center p-8">
                        <div class="relative">

                            <div class="mb-4 rounded-lg bg-white p-4 shadow-lg dark:bg-[#161615]">
                                <div class="mb-3 flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]"></div>
                                    <div class="h-2 w-16 rounded bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                                </div>
                                <div class="mb-2 flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]"></div>
                                    <div class="h-2 w-20 rounded bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]"></div>
                                    <div class="h-2 w-14 rounded bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                                </div>
                            </div>
                            
    
                            <div class="mb-4 rounded-lg bg-white p-4 shadow-lg dark:bg-[#161615]">
                                <div class="mb-2 h-2 w-full rounded-full bg-[#e3e3e0] dark:bg-[#3E3E3A]">
                                    <div class="h-2 w-2/3 rounded-full bg-[#dbdbd7] dark:bg-[#3E3E3A]"></div>
                                </div>
                                <div class="h-2 w-12 rounded bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                            </div>
                            

                            <div class="rounded-lg bg-white p-4 shadow-lg dark:bg-[#161615]">
                                <div class="mb-2 flex items-center gap-2">
                                    <div class="h-3 w-3 rounded bg-[#dbdbd7] dark:bg-[#3E3E3A]"></div>
                                    <div class="h-2 w-16 rounded bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                                </div>
                                <div class="grid grid-cols-7 gap-1">
                                    <div class="h-2 w-2 rounded bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                                    <div class="h-2 w-2 rounded bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                                    <div class="h-2 w-2 rounded bg-[#dbdbd7] dark:bg-[#3E3E3A]"></div>
                                    <div class="h-2 w-2 rounded bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                                    <div class="h-2 w-2 rounded bg-[#dbdbd7] dark:bg-[#3E3E3A]"></div>
                                    <div class="h-2 w-2 rounded bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                                    <div class="h-2 w-2 rounded bg-[#e3e3e0] dark:bg-[#3E3E3A]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="absolute inset-0 rounded-t-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:overflow-hidden lg:rounded-t-none lg:rounded-r-lg dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                    />
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>