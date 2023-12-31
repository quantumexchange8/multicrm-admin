import { useDark, useToggle } from '@vueuse/core'
import { reactive } from 'vue'

export const isDark = useDark()
export const toggleDarkMode = useToggle(isDark)

export const sidebarState = reactive({
    isOpen: window.innerWidth > 1024,
    isHovered: false,
    handleHover(value) {
        if (window.innerWidth < 1024) {
            return
        }
        sidebarState.isHovered = value
    },
    handleWindowResize() {
        if (window.innerWidth <= 1024) {
            sidebarState.isOpen = false
        } else {
            sidebarState.isOpen = true
        }
    },
})

export const scrolling = reactive({
    down: false,
    up: false,
})

let lastScrollTop = 0

export const handleScroll = () => {
    let st = window.pageYOffset || document.documentElement.scrollTop
    if (st > lastScrollTop) {
        // downscroll
        scrolling.down = true
        scrolling.up = false
    } else {
        // upscroll
        scrolling.down = false
        scrolling.up = true
        if (st == 0) {
            //  reset
            scrolling.down = false
            scrolling.up = false
        }
    }
    lastScrollTop = st <= 0 ? 0 : st // For Mobile or negative scrolling
}

export function transactionFormat() {
    function getChannelName(name) {
        if (name === 'bank') {
            return 'Bank Transfer';
        } else if (name === 'crypto') {
            return 'Cryptocurrency';
        }else if (name === 'fpx') {
            return 'FPX';
        }
    }

    function formatDate(date) {
        const formattedDate = new Date(date).toLocaleDateString('en-CA', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            timeZone: 'Asia/Kuala_Lumpur'
        });
        return formattedDate.split('-').join('/');
    }

    function getStatusClass(status) {
        if (status === 'Successful') {
            return 'success';
        } else if (status === 'Submitted') {
            return 'warning';
        } else if (status === 'Processing') {
            return 'primary';
        } else if (status === 'Rejected') {
            return 'danger';
        } else {
            return ''; // Default case or handle other statuses
        }
    }

    function formatAmount(amount) {
        return parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    const formatType = (type) => {
        const formattedType = type.replace(/([a-z])([A-Z])/g, '$1 $2');
        return formattedType.charAt(0).toUpperCase() + formattedType.slice(1);
    };

    const formatCategory = (category) => {
        // Replace underscores with a space
        const formattedType = category.replace(/_/g, ' ');

        // Split the string into words
        const words = formattedType.split(' ');

        // Capitalize the first word
        words[0] = words[0].charAt(0).toUpperCase() + words[0].slice(1);

        // Remove 's' from the last word if it's 'earnings'
        if (words[words.length - 1] === 'earnings') {
            words[words.length - 1] = 'Earning';
        }

        // Join the words back together
        return words.join(' ');
    };

    return {
        getChannelName,
        formatDate,
        getStatusClass,
        formatAmount,
        formatType,
        formatCategory,
    };
}
