import Alpine from 'alpinejs'
import './wallet-integration.js'

window.Alpine = Alpine

Alpine.start()

// Theme handling
document.addEventListener('DOMContentLoaded', () => {
    // Initialize theme
    const theme = localStorage.getItem('theme') || 'auto'
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    
    if (theme === 'dark' || (theme === 'auto' && prefersDark)) {
        document.documentElement.classList.add('dark')
    }
    
    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (localStorage.getItem('theme') === 'auto') {
            if (e.matches) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        }
    })
})

// Global utilities
window.formatNumber = function(num) {
    if (num >= 1000000000) {
        return (num / 1000000000).toFixed(1) + 'B'
    }
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M'
    }
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K'
    }
    return num.toString()
}

window.timeAgo = function(date) {
    const now = new Date()
    const past = new Date(date)
    const diffInSeconds = Math.floor((now - past) / 1000)
    
    if (diffInSeconds < 60) return 'just now'
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago'
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago'
    if (diffInSeconds < 2592000) return Math.floor(diffInSeconds / 86400) + ' days ago'
    if (diffInSeconds < 31536000) return Math.floor(diffInSeconds / 2592000) + ' months ago'
    
    return Math.floor(diffInSeconds / 31536000) + ' years ago'
}
