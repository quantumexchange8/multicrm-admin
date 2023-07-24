<script>
import {PlusCircleIcon, MinusCircleIcon} from "@heroicons/vue/solid";
export default {
    name: 'NetworkChild',
    components: {PlusCircleIcon, MinusCircleIcon},
    props: {
        members: Object,
        depth: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            expanded: false,
        }
    },
    methods: {
        nodeClicked() {
            this.expanded = !this.expanded;
            if (!this.hasChildren) {
                this.$emit('onClick', this.members);
            }
        }
    },
    computed: {
        hasChildren() {
            return this.members.children;
        },
        iconSizeClasses() {
            return this.members.parent.role === 'ib' ? 'text-[#FF9E23]' : 'text-[#007BFF]';
        },
        bgColorClass() {
            return this.members.parent === 'ib' ? 'bg-[#FF9E23] text-dark-eval-2' : 'bg-[#007BFF] text-dark-eval-2';
        },
        containerWidth() {
            // Calculate the container width as a percentage of the screen width
            const screenWidth = window.innerWidth;
            const baseWidth = 90; // Set your desired base width (percentage) here
            const breakpoint = 768; // Set your desired breakpoint (screen width) here
            const maxContainerWidth = 90; // Set the maximum container width (percentage) here

            if (screenWidth <= breakpoint) {
                // Use calc with percentage value for container width on mobile view
                return `calc(${baseWidth}% - 1rem)`;
            } else {
                // Use the maximum container width on larger screens
                return `${maxContainerWidth}%`;
            }
        },
    },
    emits: ['onClick']
}
</script>

<template>
    <div
    v-for="member in members"
        class="dark:text-white flex items-center mb-6 container"
        :style="{ 'margin-left': `${depth * 20}px`, 'width': containerWidth }"
    >
        <div v-if="hasChildren" class="mr-2" @click="nodeClicked">
            <template v-if="expanded">
                <!-- Show the MinusCircleIcon if expanded -->
                <MinusCircleIcon
                    aria-hidden="true"
                    :class="['w-8 h-8 cursor-pointer', iconSizeClasses]"
                />
            </template>
            <template v-else>
                <!-- Show the PlusCircleIcon if not expanded -->
                <PlusCircleIcon
                    aria-hidden="true"
                    :class="['w-8 h-8 cursor-pointer', iconSizeClasses]"
                />
            </template>
        </div>
        <div v-else :class="['ml-0.5 mr-2.5 w-6 h-6 grow-0 shrink-0 rounded-full', bgColorClass]"></div>
        <div class="flex items-center gap-8 p-3 overflow-x-auto rounded-lg bg-gray-50 hover:bg-gray-100 group hover:shadow dark:bg-dark-eval-1 dark:hover:bg-dark-eval-2 container">
            <a
                href="#"
                class="flex items-center space-x-4 text-base font-bold text-gray-900 dark:text-white w-64"
            >
                <img
                    class="object-cover w-12 h-12 rounded-full"
                    :src="member.profile_photo ? member.profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                    alt="userPic"
                />
                <div class="flex-col ml-3">
                      <span
                          :class="[
                          'text-xs font-bold uppercase mr-2 px-2 py-0.5 rounded',
                          bgColorClass
                        ]"
                      >{{ member.parent.role }}</span> <br>
                    <span>{{ member.parent.first_name }}</span> <br>
                    <span class="text-xs dark:text-dark-eval-4">{{ member.parent.email }}</span>
                </div>
            </a>
            <div class="flex items-center justify-between w-full gap-2">
                <div class="inline-block h-auto min-h-[3em] w-0.5 self-stretch bg-dark-eval-4 dark:bg-[#202020] opacity-100 mx-3 my-1"></div>
                <div class="flex flex-col text-center">
                    <span>{{ member.level }}</span>
                    <span class="text-xs dark:text-dark-eval-4">Level</span>
                </div>
                <div class="flex flex-col text-center">
                    <span>{{ member.parent.total_group_deposit }}</span>
                    <span class="text-xs dark:text-dark-eval-4">Total Group Deposit</span>
                </div>
                <div class="flex flex-col text-center">
                    <span>{{ member.parent.total_group_withdrawal }}</span>
                    <span class="text-xs dark:text-dark-eval-4">Total Group Withdrawal</span>
                </div>
                <div class="flex flex-col text-center">
                    <span>{{ member.total_ib }}</span>
                    <span class="text-xs dark:text-dark-eval-4">Total IB</span>
                </div>
                <div class="flex flex-col text-center">
                    <span>{{ member.total_client }}</span>
                    <span class="text-xs dark:text-dark-eval-4">Total Client</span>
                </div>
            </div>
        </div>
    </div>
    <NetworkChild
        v-if="expanded"
        v-for="(child, index) in members.children"
        :key="child.id"
        :members="child"
        :depth="depth + 1"
        @onClick="nodeClicked"
    />
</template>
