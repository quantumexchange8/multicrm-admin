// Extra icons

import { defineComponent } from 'vue'

export const MenuFoldLineRightIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 24 24"
                stroke="currentColor"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M12 6H20M12 12H20M4 18H20M4 6L8 9L4 12"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        )
    },
})

export const MenuFoldLineLeftIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 24 24"
                stroke="currentColor"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M12 6H20M12 12H20M4 18H20M8 6L4 9L8 12"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        )
    },
})

export const DashboardIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 24 24"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path d="M5.5 12.5C5.22386 12.5 5 12.7239 5 13C5 13.2761 5.22386 13.5 5.5 13.5H7C7.27614 13.5 7.5 13.2761 7.5 13C7.5 12.7239 7.27614 12.5 7 12.5H5.5Z" />
                <path d="M6.93935 7.93935C7.13462 7.74409 7.4512 7.74409 7.64646 7.93935L8.70712 9.00001C8.90238 9.19528 8.90238 9.51186 8.70712 9.70712C8.51186 9.90238 8.19528 9.90238 8.00001 9.70712L6.93935 8.64646C6.74409 8.4512 6.74409 8.13462 6.93935 7.93935Z" />
                <path d="M17 12.5C16.7239 12.5 16.5 12.7239 16.5 13C16.5 13.2761 16.7239 13.5 17 13.5H18.5C18.7761 13.5 19 13.2761 19 13C19 12.7239 18.7761 12.5 18.5 12.5H17Z" />
                <path d="M15.4394 10.0606C15.2441 9.86538 15.2441 9.5488 15.4394 9.35354L16.5 8.29288C16.6953 8.09762 17.0119 8.09762 17.2071 8.29288C17.4024 8.48814 17.4024 8.80472 17.2071 8.99999L16.1465 10.0606C15.9512 10.2559 15.6346 10.2559 15.4394 10.0606Z" />
                <path d="M12.9661 12.741C12.9882 12.8236 13 12.9104 13 13C13 13.5523 12.5523 14 12 14C11.4477 14 11 13.5523 11 13C11 12.4477 11.4477 12 12 12C12.0896 12 12.1764 12.0118 12.259 12.0339L13 11.2929C13.1953 11.0976 13.5119 11.0976 13.7071 11.2929C13.9024 11.4881 13.9024 11.8047 13.7071 12L12.9661 12.741Z" />
                <path d="M12 8.5C11.7239 8.5 11.5 8.27614 11.5 8V6.5C11.5 6.22386 11.7239 6 12 6C12.2761 6 12.5 6.22386 12.5 6.5V8C12.5 8.27614 12.2761 8.5 12 8.5Z" />
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M12 3C9.34784 3 6.8043 4.05357 4.92893 5.92893C3.05357 7.8043 2 10.3478 2 13C2 14.3132 2.25866 15.6136 2.7612 16.8268C3.26375 18.0401 4.00035 19.1425 4.92893 20.0711C5.11647 20.2586 5.37082 20.364 5.63604 20.364H18.364C18.6292 20.364 18.8835 20.2586 19.0711 20.0711C19.9997 19.1425 20.7362 18.0401 21.2388 16.8268C21.7413 15.6136 22 14.3132 22 13C22 10.3478 20.9464 7.8043 19.0711 5.92893C17.1957 4.05357 14.6522 3 12 3ZM6.34315 7.34315C7.84344 5.84285 9.87827 5 12 5C14.1217 5 16.1566 5.84285 17.6569 7.34315C19.1571 8.84344 20 10.8783 20 13C20 14.0506 19.7931 15.0909 19.391 16.0615C19.0406 16.9075 18.5479 17.6861 17.9353 18.364H6.06469C5.45205 17.6861 4.95938 16.9075 4.60896 16.0615C4.20693 15.0909 4 14.0506 4 13C4 10.8783 4.84285 8.84344 6.34315 7.34315Z"
                />
            </svg>
        )
    },
})

export const ArrowsInnerIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 24 24"
                stroke="currentColor"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M4 4L9 9M9 9V5M9 9H5M20 4L15 9M15 9V5M15 9H19M4 20L9 15M9 15H5M9 15V19M20 20L15 15M15 15H19M15 15V19"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        )
    },
})

export const EmptyCircleIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M20.3149 15.4442C20.7672 14.3522 21 13.1819 21 12C21 9.61305 20.0518 7.32387 18.364 5.63604C16.6761 3.94821 14.3869 3 12 3C9.61305 3 7.32387 3.94821 5.63604 5.63604C3.94821 7.32387 3 9.61305 3 12C3 13.1819 3.23279 14.3522 3.68508 15.4442C4.13738 16.5361 4.80031 17.5282 5.63604 18.364C6.47177 19.1997 7.46392 19.8626 8.55585 20.3149C9.64778 20.7672 10.8181 21 12 21C13.1819 21 14.3522 20.7672 15.4442 20.3149C16.5361 19.8626 17.5282 19.1997 18.364 18.364C19.1997 17.5282 19.8626 16.5361 20.3149 15.4442Z"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        )
    },
})

export const ViewIcon = defineComponent( {
    setup() {
        return () => (
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 13.5C13.1421 13.5 16.5 9 16.5 9C16.5 9 13.1421 4.5 9 4.5C4.85786 4.5 1.5 9 1.5 9C1.5 9 4.85786 13.5 9 13.5Z" stroke="#007BFF" stroke-width="1.5" stroke-linejoin="round"/>
                <path d="M9 10.875C10.0355 10.875 10.875 10.0355 10.875 9C10.875 7.96448 10.0355 7.125 9 7.125C7.96448 7.125 7.125 7.96448 7.125 9C7.125 10.0355 7.96448 10.875 9 10.875Z" fill="#007BFF" stroke="#007BFF" stroke-width="1.5" stroke-linejoin="round"/>
            </svg>
        )
    }
})
export const ResetPasswordIcon = defineComponent( {
    setup() {
        return () => (
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.75006 15.75C8.97507 15.75 8.24057 15.628 7.54657 15.384C6.85307 15.1405 6.21882 14.8 5.64382 14.3625C5.46882 14.225 5.37507 14.047 5.36257 13.8285C5.35007 13.6095 5.43131 13.4125 5.60632 13.2375C5.74381 13.1 5.90631 13.0312 6.09381 13.0312C6.28131 13.0312 6.45632 13.0875 6.61881 13.2C7.05631 13.525 7.53757 13.7812 8.06256 13.9688C8.58756 14.1562 9.15007 14.25 9.75006 14.25C11.2001 14.25 12.4376 13.7375 13.4626 12.7125C14.4876 11.6875 15.0001 10.45 15.0001 9C15.0001 7.55 14.4876 6.3125 13.4626 5.2875C12.4376 4.2625 11.2001 3.75 9.75006 3.75C8.30007 3.75 7.06257 4.2625 6.03757 5.2875C5.01257 6.3125 4.50007 7.55 4.50007 9V9.13125L5.34381 8.2875C5.48132 8.15 5.65306 8.08125 5.85907 8.08125C6.06557 8.08125 6.24381 8.15 6.39382 8.2875C6.54381 8.4375 6.61881 8.6155 6.61881 8.8215C6.61881 9.028 6.54381 9.20625 6.39382 9.35625L4.27506 11.475C4.20006 11.55 4.11882 11.603 4.03132 11.634C3.94381 11.6655 3.85006 11.6812 3.75007 11.6812C3.65007 11.6812 3.55632 11.6655 3.46882 11.634C3.38131 11.603 3.30007 11.55 3.22507 11.475L1.08757 9.3375C0.950065 9.2 0.884565 9.025 0.891065 8.8125C0.897065 8.6 0.968815 8.425 1.10632 8.2875C1.24382 8.1375 1.41882 8.0625 1.63132 8.0625C1.84382 8.0625 2.01882 8.1375 2.15632 8.2875L3.00007 9.15V9C3.00007 8.0625 3.17831 7.18425 3.53481 6.36525C3.89082 5.54675 4.37207 4.83425 4.97857 4.22775C5.58457 3.62175 6.29706 3.1405 7.11607 2.784C7.93457 2.428 8.81256 2.25 9.75006 2.25C10.6876 2.25 11.5658 2.428 12.3848 2.784C13.2033 3.1405 13.9158 3.62175 14.5223 4.22775C15.1283 4.83425 15.6096 5.54675 15.9661 6.36525C16.3221 7.18425 16.5001 8.0625 16.5001 9C16.5001 10.875 15.8438 12.4688 14.5313 13.7812C13.2188 15.0938 11.6251 15.75 9.75006 15.75ZM8.25006 12C8.03757 12 7.85957 11.928 7.71607 11.784C7.57207 11.6405 7.50006 11.4625 7.50006 11.25V9C7.50006 8.7875 7.57207 8.60925 7.71607 8.46525C7.85957 8.32175 8.03757 8.25 8.25006 8.25V7.5C8.25006 7.0875 8.39707 6.73425 8.69107 6.44025C8.98457 6.14675 9.33757 6 9.75006 6C10.1626 6 10.5158 6.14675 10.8098 6.44025C11.1033 6.73425 11.2501 7.0875 11.2501 7.5V8.25C11.4626 8.25 11.6408 8.32175 11.7848 8.46525C11.9283 8.60925 12.0001 8.7875 12.0001 9V11.25C12.0001 11.4625 11.9283 11.6405 11.7848 11.784C11.6408 11.928 11.4626 12 11.2501 12H8.25006ZM9.00006 8.25H10.5001V7.5C10.5001 7.2875 10.4283 7.10925 10.2848 6.96525C10.1408 6.82175 9.96257 6.75 9.75006 6.75C9.53757 6.75 9.35956 6.82175 9.21607 6.96525C9.07207 7.10925 9.00006 7.2875 9.00006 7.5V8.25Z" fill="#007BFF"/>
            </svg>
        )
    }
})
export const TrashIcon = defineComponent( {
    setup() {
        return () => (
            <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.40039 11.25L8.40039 9" stroke="#FF3F34" stroke-width="1.33333" stroke-linecap="round"/>
                <path d="M11.4004 11.25L11.4004 9" stroke="#FF3F34" stroke-width="1.33333" stroke-linecap="round"/>
                <path d="M3.15039 5.25H16.6504V5.25C15.7933 5.25 15.3647 5.25 15.0483 5.4397C14.8602 5.55242 14.7028 5.70979 14.5901 5.89786C14.4004 6.21435 14.4004 6.6429 14.4004 7.5V12.3333C14.4004 13.5904 14.4004 14.219 14.0099 14.6095C13.6193 15 12.9908 15 11.7337 15H8.06706C6.80998 15 6.18144 15 5.79091 14.6095C5.40039 14.219 5.40039 13.5904 5.40039 12.3333V7.5C5.40039 6.6429 5.40039 6.21435 5.2107 5.89786C5.09797 5.70979 4.9406 5.55242 4.75253 5.4397C4.43604 5.25 4.00749 5.25 3.15039 5.25V5.25Z" stroke="#FF3F34" stroke-width="1.33333" stroke-linecap="round"/>
                <path d="M8.4515 2.52794C8.53696 2.44821 8.72528 2.37775 8.98725 2.32749C9.24921 2.27724 9.57019 2.25 9.90039 2.25C10.2306 2.25 10.5516 2.27724 10.8135 2.32749C11.0755 2.37775 11.2638 2.44821 11.3493 2.52794" stroke="#FF3F34" stroke-width="1.33333" stroke-linecap="round"/>
            </svg>
        )
    }
})
export const GearIcon = defineComponent( {
    setup() {
        return () => (
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.01598 10.4304C9.34079 10.4304 10.4148 9.35642 10.4148 8.0316C10.4148 6.70679 9.34079 5.63281 8.01598 5.63281C6.69116 5.63281 5.61719 6.70679 5.61719 8.0316C5.61719 9.35642 6.69116 10.4304 8.01598 10.4304Z" fill="#007BFF" stroke="#007BFF" stroke-width="0.280967" stroke-miterlimit="10"/>
                <path d="M8.0134 12.9877C10.7801 12.9877 13.0229 10.7449 13.0229 7.97824C13.0229 5.21158 10.7801 2.96875 8.0134 2.96875C5.24673 2.96875 3.00391 5.21158 3.00391 7.97824C3.00391 10.7449 5.24673 12.9877 8.0134 12.9877Z" stroke="#007BFF" stroke-width="0.5" stroke-miterlimit="10"/>
                <path d="M4.46732 4.4932L3.83962 2.69281C3.79339 2.61244 3.78091 2.51701 3.80491 2.42744C3.82892 2.33788 3.88745 2.26148 3.96769 2.21501L5.15244 1.53103C5.23282 1.4848 5.32825 1.47231 5.41781 1.49632C5.50738 1.52032 5.58377 1.57886 5.63025 1.6591L6.93675 3.11491" fill="#007BFF"/>
                <path d="M4.46732 4.4932L3.83962 2.69281C3.79339 2.61244 3.78091 2.51701 3.80491 2.42744C3.82892 2.33788 3.88745 2.26148 3.96769 2.21501L5.15244 1.53103C5.23282 1.4848 5.32825 1.47231 5.41781 1.49632C5.50738 1.52032 5.58377 1.57886 5.63025 1.6591L6.93675 3.11491" stroke="#007BFF" stroke-width="0.255424" stroke-miterlimit="10"/>
                <path d="M10.6892 3.69354L10.8277 1.79266C10.8515 1.70295 10.8388 1.60745 10.7922 1.52712C10.7457 1.44679 10.6692 1.38821 10.5796 1.36424L9.2587 1.01163C9.169 0.987943 9.07357 1.00075 8.99328 1.04724C8.913 1.09373 8.85439 1.17013 8.83029 1.25972L7.94531 3.00497" fill="#007BFF"/>
                <path d="M10.6892 3.69354L10.8277 1.79266C10.8515 1.70295 10.8388 1.60745 10.7922 1.52712C10.7457 1.44679 10.6692 1.38821 10.5796 1.36424L9.2587 1.01163C9.169 0.987943 9.07357 1.00075 8.99328 1.04724C8.913 1.09373 8.85439 1.17013 8.83029 1.25972L7.94531 3.00497" stroke="#007BFF" stroke-width="0.255424" stroke-miterlimit="10"/>
                <path d="M13.0622 6.81735L14.4433 5.50339C14.4819 5.47851 14.5153 5.44626 14.5414 5.40849C14.5676 5.37072 14.5861 5.32817 14.5958 5.28327C14.6055 5.23836 14.6062 5.19199 14.598 5.14679C14.5898 5.10159 14.5727 5.05845 14.5478 5.01984L13.807 3.87127C13.7821 3.83265 13.7499 3.79931 13.7121 3.77315C13.6744 3.747 13.6318 3.72854 13.5869 3.71883C13.542 3.70913 13.4956 3.70836 13.4504 3.71658C13.4052 3.72481 13.3621 3.74185 13.3235 3.76675L11.4961 4.46278" fill="#007BFF"/>
                <path d="M13.0622 6.81735L14.4433 5.50339C14.4819 5.47851 14.5153 5.44626 14.5414 5.40849C14.5676 5.37072 14.5861 5.32817 14.5958 5.28327C14.6055 5.23836 14.6062 5.19199 14.598 5.14679C14.5898 5.10159 14.5727 5.05845 14.5478 5.01984L13.807 3.87127C13.7821 3.83265 13.7499 3.79931 13.7121 3.77315C13.6744 3.747 13.6318 3.72854 13.5869 3.71883C13.542 3.70913 13.4956 3.70836 13.4504 3.71658C13.4052 3.72481 13.3621 3.74185 13.3235 3.76675L11.4961 4.46278" stroke="#007BFF" stroke-width="0.255424" stroke-miterlimit="10"/>
                <path d="M12.332 10.6165L14.2272 10.8181C14.316 10.8448 14.4119 10.8351 14.4937 10.7913C14.5754 10.7475 14.6366 10.6731 14.6636 10.5844L15.0599 9.27557C15.0867 9.1867 15.0772 9.09082 15.0333 9.00898C14.9895 8.92715 14.915 8.86605 14.8262 8.83912L13.1136 7.89844" fill="#007BFF"/>
                <path d="M12.332 10.6165L14.2272 10.8181C14.316 10.8448 14.4119 10.8351 14.4937 10.7913C14.5754 10.7475 14.6366 10.6731 14.6636 10.5844L15.0599 9.27557C15.0867 9.1867 15.0772 9.09082 15.0333 9.00898C14.9895 8.92715 14.915 8.86605 14.8262 8.83912L13.1136 7.89844" stroke="#007BFF" stroke-width="0.255424" stroke-miterlimit="10"/>
                <path d="M9.25781 12.8941L10.5115 14.3298C10.5584 14.4099 10.6353 14.468 10.7251 14.4915C10.8149 14.515 10.9103 14.5019 10.9904 14.455L12.1706 13.7659C12.2507 13.7189 12.3088 13.6421 12.3323 13.5523C12.3557 13.4625 12.3426 13.3671 12.2958 13.2869L11.6779 11.4297" fill="#007BFF"/>
                <path d="M9.25781 12.8941L10.5115 14.3298C10.5584 14.4099 10.6353 14.468 10.7251 14.4915C10.8149 14.515 10.9103 14.5019 10.9904 14.455L12.1706 13.7659C12.2507 13.7189 12.3088 13.6421 12.3323 13.5523C12.3557 13.4625 12.3426 13.3671 12.2958 13.2869L11.6779 11.4297" stroke="#007BFF" stroke-width="0.255424" stroke-miterlimit="10"/>
                <path d="M5.41681 12.2891L5.25888 14.1894C5.24657 14.2336 5.2431 14.2799 5.24868 14.3255C5.25426 14.3711 5.26877 14.4152 5.29138 14.4552C5.31399 14.4952 5.34426 14.5303 5.38047 14.5586C5.41667 14.5869 5.4581 14.6078 5.50237 14.6201L6.81979 14.9859C6.86409 14.9984 6.91043 15.002 6.95613 14.9965C7.00183 14.991 7.04599 14.9765 7.08607 14.9539C7.12616 14.9312 7.16137 14.9009 7.18969 14.8646C7.218 14.8283 7.23887 14.7868 7.25107 14.7424L8.15213 13.0069" fill="#007BFF"/>
                <path d="M5.41681 12.2891L5.25888 14.1894C5.24657 14.2336 5.2431 14.2799 5.24868 14.3255C5.25426 14.3711 5.26877 14.4152 5.29138 14.4552C5.31399 14.4952 5.34426 14.5303 5.38047 14.5586C5.41667 14.5869 5.4581 14.6078 5.50237 14.6201L6.81979 14.9859C6.86409 14.9984 6.91043 15.002 6.95613 14.9965C7.00183 14.991 7.04599 14.9765 7.08607 14.9539C7.12616 14.9312 7.16137 14.9009 7.18969 14.8646C7.218 14.8283 7.23887 14.7868 7.25107 14.7424L8.15213 13.0069" stroke="#007BFF" stroke-width="0.255424" stroke-miterlimit="10"/>
                <path d="M3.06238 9.15625L1.64275 10.4283C1.6032 10.452 1.56872 10.4833 1.5413 10.5204C1.51388 10.5575 1.49405 10.5996 1.48295 10.6444C1.47185 10.6892 1.4697 10.7357 1.47663 10.7813C1.48355 10.8269 1.49941 10.8707 1.5233 10.9101L2.22967 12.0811C2.25329 12.1205 2.28446 12.1549 2.32139 12.1823C2.35833 12.2096 2.40031 12.2294 2.44491 12.2405C2.48952 12.2516 2.53588 12.2538 2.58133 12.2469C2.62678 12.2401 2.67043 12.2243 2.70977 12.2005L4.55552 11.5568" fill="#007BFF"/>
                <path d="M3.06238 9.15625L1.64275 10.4283C1.6032 10.452 1.56872 10.4833 1.5413 10.5204C1.51388 10.5575 1.49405 10.5996 1.48295 10.6444C1.47185 10.6892 1.4697 10.7357 1.47663 10.7813C1.48355 10.8269 1.49941 10.8707 1.5233 10.9101L2.22967 12.0811C2.25329 12.1205 2.28446 12.1549 2.32139 12.1823C2.35833 12.2096 2.40031 12.2294 2.44491 12.2405C2.48952 12.2516 2.53588 12.2538 2.58133 12.2469C2.62678 12.2401 2.67043 12.2243 2.70977 12.2005L4.55552 11.5568" stroke="#007BFF" stroke-width="0.255424" stroke-miterlimit="10"/>
                <path d="M3.72939 5.40202L1.83425 5.21767C1.78998 5.20449 1.74354 5.20021 1.69761 5.20507C1.65168 5.20993 1.60717 5.22384 1.56664 5.246C1.52612 5.26816 1.49038 5.29812 1.4615 5.33417C1.43261 5.37021 1.41116 5.41162 1.39837 5.456L1.01417 6.76882C1.00123 6.8129 0.997092 6.8591 1.00201 6.90478C1.00692 6.95046 1.02079 6.99472 1.04281 7.03504C1.06484 7.07536 1.09459 7.11094 1.13037 7.13975C1.16615 7.16857 1.20726 7.19006 1.25135 7.20298L2.97421 8.12815" fill="#007BFF"/>
                <path d="M3.72939 5.40202L1.83425 5.21767C1.78998 5.20449 1.74354 5.20021 1.69761 5.20507C1.65168 5.20993 1.60717 5.22384 1.56664 5.246C1.52612 5.26816 1.49038 5.29812 1.4615 5.33417C1.43261 5.37021 1.41116 5.41162 1.39837 5.456L1.01417 6.76882C1.00123 6.8129 0.997092 6.8591 1.00201 6.90478C1.00692 6.95046 1.02079 6.99472 1.04281 7.03504C1.06484 7.07536 1.09459 7.11094 1.13037 7.13975C1.16615 7.16857 1.20726 7.19006 1.25135 7.20298L2.97421 8.12815" stroke="#007BFF" stroke-width="0.255424" stroke-miterlimit="10"/>
            </svg>

        )
    }
})
export const IbTransferIcon = defineComponent( {
    setup() {
        return () => (
            <svg width="21" height="18" viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="5.50601" cy="6.77945" r="1.61538" stroke="#007BFF" stroke-width="1.125" stroke-linecap="round"/>
                <path d="M2.81614 11.3542C3.08312 10.226 4.20288 9.60938 5.36227 9.60938H5.65095C6.81034 9.60938 7.9301 10.226 8.19708 11.3542C8.24874 11.5725 8.2898 11.801 8.3129 12.0331C8.33498 12.2551 8.15273 12.4363 7.92969 12.4363H3.08353C2.8605 12.4363 2.67824 12.2551 2.70033 12.0331C2.72343 11.801 2.76448 11.5725 2.81614 11.3542Z" stroke="#007BFF" stroke-width="1.125" stroke-linecap="round"/>
                <circle cx="15.1974" cy="6.77945" r="1.61538" stroke="#007BFF" stroke-width="1.125" stroke-linecap="round"/>
                <path d="M12.5075 11.3542C12.7745 10.226 13.8943 9.60938 15.0537 9.60938H15.3424C16.5017 9.60938 17.6215 10.226 17.8885 11.3542C17.9401 11.5725 17.9812 11.801 18.0043 12.0331C18.0264 12.2551 17.8441 12.4363 17.6211 12.4363H12.7749C12.5519 12.4363 12.3696 12.2551 12.3917 12.0331C12.4148 11.801 12.4559 11.5725 12.5075 11.3542Z" stroke="#007BFF" stroke-width="1.125" stroke-linecap="round"/>
                <path d="M6.33823 3.14188C6.10944 3.35203 6.09433 3.70786 6.30449 3.93665C6.51464 4.16545 6.87047 4.18055 7.09927 3.9704L6.33823 3.14188ZM7.09927 3.9704C8.25718 2.90681 9.22973 2.4634 10.1888 2.46876C11.1648 2.47421 12.2559 2.94371 13.6471 4.00359L14.3289 3.10869C12.8665 1.9946 11.5344 1.35125 10.195 1.34378C8.83865 1.3362 7.60339 1.97978 6.33823 3.14188L7.09927 3.9704Z" fill="#007BFF"/>
                <path d="M11.9688 3.95433L13.988 3.55048L13.5841 1.53125" stroke="#007BFF" stroke-width="1.125" stroke-linecap="round"/>
                <path d="M14.3688 14.8581C14.5976 14.648 14.6127 14.2921 14.4025 14.0633C14.1924 13.8346 13.8366 13.8194 13.6078 14.0296L14.3688 14.8581ZM13.6078 14.0296C12.4498 15.0932 11.4773 15.5366 10.5183 15.5312C9.54222 15.5258 8.45116 15.0563 7.05993 13.9964L6.37817 14.8913C7.84057 16.0054 9.17259 16.6487 10.512 16.6562C11.8684 16.6638 13.1036 16.0202 14.3688 14.8581L13.6078 14.0296Z" fill="#007BFF"/>
                <path d="M8.73828 14.0457L6.71905 14.4495L7.1229 16.4688" stroke="#007BFF" stroke-width="1.125" stroke-linecap="round"/>
            </svg>

        )
    }
})
