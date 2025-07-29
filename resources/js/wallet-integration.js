class WalletManager {
    constructor() {
        this.connectedWallets = [];
        this.supportedWallets = {
            ethereum: {
                metamask: {
                    name: 'MetaMask',
                    icon: '🦊',
                    detectMethod: () => window.ethereum && window.ethereum.isMetaMask,
                    connectMethod: this.connectMetaMask.bind(this)
                },
                walletconnect: {
                    name: 'WalletConnect',
                    icon: '🔗',
                    detectMethod: () => true, // Always available
                    connectMethod: this.connectWalletConnect.bind(this)
                },
                coinbase: {
                    name: 'Coinbase Wallet',
                    icon: '💙',
                    detectMethod: () => window.ethereum && window.ethereum.isCoinbaseWallet,
                    connectMethod: this.connectCoinbase.bind(this)
                }
            },
            solana: {
                phantom: {
                    name: 'Phantom',
                    icon: '👻',
                    detectMethod: () => window.solana && window.solana.isPhantom,
                    connectMethod: this.connectPhantom.bind(this)
                },
                solflare: {
                    name: 'Solflare',
                    icon: '☀️',
                    detectMethod: () => window.solflare,
                    connectMethod: this.connectSolflare.bind(this)
                }
            },
            cosmos: {
                keplr: {
                    name: 'Keplr',
                    icon: '🌌',
                    detectMethod: () => window.keplr,
                    connectMethod: this.connectKeplr.bind(this)
                }
            }
        };

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.detectWallets();
    }

    setupEventListeners() {
        // Listen for wallet connection buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-wallet-connect]')) {
                e.preventDefault();
                const blockchain = e.target.dataset.blockchain;
                const walletType = e.target.dataset.walletType;
                this.connectWallet(blockchain, walletType);
            }

            if (e.target.matches('[data-wallet-disconnect]')) {
                e.preventDefault();
                const address = e.target.dataset.address;
                const blockchain = e.target.dataset.blockchain;
                this.disconnectWallet(address, blockchain);
            }
        });

        // Listen for account changes
        if (window.ethereum) {
            window.ethereum.on('accountsChanged', (accounts) => {
                this.handleAccountsChanged(accounts);
            });

            window.ethereum.on('chainChanged', (chainId) => {
                this.handleChainChanged(chainId);
            });
        }
    }

    detectWallets() {
        const availableWallets = {};

        Object.keys(this.supportedWallets).forEach(blockchain => {
            availableWallets[blockchain] = {};
            Object.keys(this.supportedWallets[blockchain]).forEach(walletType => {
                const wallet = this.supportedWallets[blockchain][walletType];
                if (wallet.detectMethod()) {
                    availableWallets[blockchain][walletType] = wallet;
                }
            });
        });

        this.updateWalletUI(availableWallets);
    }

    updateWalletUI(availableWallets) {
        // Update wallet connection buttons
        document.querySelectorAll('[data-wallet-list]').forEach(container => {
            const blockchain = container.dataset.blockchain;
            if (availableWallets[blockchain]) {
                this.renderWalletButtons(container, blockchain, availableWallets[blockchain]);
            }
        });
    }

    renderWalletButtons(container, blockchain, wallets) {
        container.innerHTML = '';
        
        Object.keys(wallets).forEach(walletType => {
            const wallet = wallets[walletType];
            const button = document.createElement('button');
            button.className = 'flex items-center space-x-3 w-full p-4 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200';
            button.dataset.walletConnect = '';
            button.dataset.blockchain = blockchain;
            button.dataset.walletType = walletType;
            
            button.innerHTML = `
                <span class="text-2xl">${wallet.icon}</span>
                <div class="flex-1 text-left">
                    <div class="font-medium text-gray-900 dark:text-white">${wallet.name}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Connect to ${blockchain}</div>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            `;
            
            container.appendChild(button);
        });
    }

    async connectWallet(blockchain, walletType) {
        try {
            this.showLoading(`Connecting to ${walletType}...`);
            
            const wallet = this.supportedWallets[blockchain][walletType];
            if (!wallet) {
                throw new Error('Wallet not supported');
            }

            const connection = await wallet.connectMethod(blockchain);
            await this.authenticateWallet(connection);
            
            this.hideLoading();
            this.showSuccess('Wallet connected successfully!');
            
        } catch (error) {
            this.hideLoading();
            this.showError(error.message || 'Failed to connect wallet');
        }
    }

    async connectMetaMask(blockchain) {
        if (!window.ethereum) {
            throw new Error('MetaMask not installed');
        }

        const accounts = await window.ethereum.request({
            method: 'eth_requestAccounts'
        });

        if (accounts.length === 0) {
            throw new Error('No accounts available');
        }

        const address = accounts[0];
        const chainId = await window.ethereum.request({ method: 'eth_chainId' });

        return {
            address,
            walletType: 'metamask',
            blockchain,
            chainId
        };
    }

    async connectPhantom(blockchain) {
        if (!window.solana || !window.solana.isPhantom) {
            throw new Error('Phantom wallet not installed');
        }

        const response = await window.solana.connect();
        
        return {
            address: response.publicKey.toString(),
            walletType: 'phantom',
            blockchain,
            publicKey: response.publicKey
        };
    }

    async connectKeplr(blockchain) {
        if (!window.keplr) {
            throw new Error('Keplr wallet not installed');
        }

        const chainId = 'cosmoshub-4'; // Default to Cosmos Hub
        await window.keplr.enable(chainId);
        
        const offlineSigner = window.keplr.getOfflineSigner(chainId);
        const accounts = await offlineSigner.getAccounts();
        
        if (accounts.length === 0) {
            throw new Error('No accounts available');
        }

        return {
            address: accounts[0].address,
            walletType: 'keplr',
            blockchain,
            chainId
        };
    }

    async connectWalletConnect(blockchain) {
        // WalletConnect implementation would go here
        // For now, throw an error
        throw new Error('WalletConnect not implemented yet');
    }

    async connectCoinbase(blockchain) {
        if (!window.ethereum || !window.ethereum.isCoinbaseWallet) {
            throw new Error('Coinbase Wallet not installed');
        }

        const accounts = await window.ethereum.request({
            method: 'eth_requestAccounts'
        });

        if (accounts.length === 0) {
            throw new Error('No accounts available');
        }

        return {
            address: accounts[0],
            walletType: 'coinbase',
            blockchain
        };
    }

    async connectSolflare(blockchain) {
        if (!window.solflare) {
            throw new Error('Solflare wallet not installed');
        }

        await window.solflare.connect();
        
        return {
            address: window.solflare.publicKey.toString(),
            walletType: 'solflare',
            blockchain,
            publicKey: window.solflare.publicKey
        };
    }

    async authenticateWallet(connection) {
        // Get nonce from server
        const nonceResponse = await fetch('/auth/wallet/nonce', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                address: connection.address
            })
        });

        if (!nonceResponse.ok) {
            throw new Error('Failed to get authentication nonce');
        }

        const { nonce, message } = await nonceResponse.json();

        // Sign message
        let signature;
        switch (connection.blockchain) {
            case 'ethereum':
            case 'polygon':
            case 'bsc':
            case 'arbitrum':
                signature = await this.signEthereumMessage(message);
                break;
            case 'solana':
                signature = await this.signSolanaMessage(message, connection);
                break;
            case 'cosmos':
                signature = await this.signCosmosMessage(message, connection);
                break;
            default:
                throw new Error('Unsupported blockchain');
        }

        // Send authentication request
        const authResponse = await fetch('/auth/wallet/connect', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                address: connection.address,
                blockchain_slug: connection.blockchain,
                wallet_type: connection.walletType,
                signature,
                message
            })
        });

        if (!authResponse.ok) {
            const error = await authResponse.json();
            throw new Error(error.message || 'Authentication failed');
        }

        const result = await authResponse.json();
        
        // Reload page to update authentication state
        window.location.reload();
        
        return result;
    }

    async signEthereumMessage(message) {
        if (!window.ethereum) {
            throw new Error('Ethereum provider not available');
        }

        const accounts = await window.ethereum.request({ method: 'eth_accounts' });
        if (accounts.length === 0) {
            throw new Error('No accounts connected');
        }

        return await window.ethereum.request({
            method: 'personal_sign',
            params: [message, accounts[0]]
        });
    }

    async signSolanaMessage(message, connection) {
        const encodedMessage = new TextEncoder().encode(message);
        let signedMessage;

        if (connection.walletType === 'phantom') {
            signedMessage = await window.solana.signMessage(encodedMessage, 'utf8');
        } else if (connection.walletType === 'solflare') {
            signedMessage = await window.solflare.signMessage(encodedMessage);
        } else {
            throw new Error('Unsupported Solana wallet');
        }

        return Array.from(signedMessage.signature);
    }

    async signCosmosMessage(message, connection) {
        if (!window.keplr) {
            throw new Error('Keplr not available');
        }

        const chainId = 'cosmoshub-4';
        const result = await window.keplr.signArbitrary(
            chainId,
            connection.address,
            message
        );

        return result.signature;
    }

    async disconnectWallet(address, blockchain) {
        try {
            const response = await fetch('/auth/wallet/disconnect', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    address,
                    blockchain_slug: blockchain
                })
            });

            if (!response.ok) {
                throw new Error('Failed to disconnect wallet');
            }

            this.showSuccess('Wallet disconnected successfully!');
            
            // Reload page to update UI
            setTimeout(() => {
                window.location.reload();
            }, 1000);

        } catch (error) {
            this.showError(error.message || 'Failed to disconnect wallet');
        }
    }

    handleAccountsChanged(accounts) {
        if (accounts.length === 0) {
            // User disconnected their wallet
            this.showInfo('Wallet disconnected');
        } else {
            // User switched accounts
            this.showInfo('Account changed');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }

    handleChainChanged(chainId) {
        // Reload page when chain changes
        window.location.reload();
    }

    showLoading(message) {
        this.hideAllNotifications();
        this.showNotification(message, 'info', true);
    }

    hideLoading() {
        document.querySelectorAll('.notification.loading').forEach(el => el.remove());
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showInfo(message) {
        this.showNotification(message, 'info');
    }

    showNotification(message, type, loading = false) {
        const notification = document.createElement('div');
        notification.className = `notification ${type} ${loading ? 'loading' : ''} fixed top-4 right-4 max-w-sm w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg p-4 z-50 transform transition-all duration-300 translate-x-full`;
        
        const colors = {
            success: 'border-green-500 bg-green-50 dark:bg-green-900/20',
            error: 'border-red-500 bg-red-50 dark:bg-red-900/20',
            info: 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
        };

        notification.className += ` ${colors[type]}`;

        const icon = {
            success: '✅',
            error: '❌',
            info: loading ? '⏳' : 'ℹ️'
        };

        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <span class="text-lg">${icon[type]}</span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">${message}</p>
                </div>
                ${!loading ? `
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                ` : ''}
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto remove (except loading notifications)
        if (!loading) {
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }
    }

    hideAllNotifications() {
        document.querySelectorAll('.notification').forEach(el => el.remove());
    }
}

// Airdrop interaction functionality
class AirdropInteractions {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-airdrop-subscribe]')) {
                e.preventDefault();
                this.toggleSubscription(e.target);
            }

            if (e.target.matches('[data-airdrop-favorite]')) {
                e.preventDefault();
                this.toggleFavorite(e.target);
            }

            if (e.target.matches('[data-airdrop-rate]')) {
                e.preventDefault();
                this.showRatingModal(e.target);
            }
        });
    }

    async toggleSubscription(button) {
        const airdropId = button.dataset.airdropId;
        const isSubscribed = button.dataset.subscribed === 'true';

        try {
            const response = await fetch(`/airdrops/${airdropId}/subscribe`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                throw new Error('Failed to update subscription');
            }

            const result = await response.json();
            
            // Update button state
            button.dataset.subscribed = result.subscribed;
            button.textContent = result.subscribed ? 'Unsubscribe' : 'Subscribe';
            button.className = result.subscribed 
                ? button.className.replace('bg-blue-600', 'bg-gray-600')
                : button.className.replace('bg-gray-600', 'bg-blue-600');

            this.showSuccess(result.message);

        } catch (error) {
            this.showError(error.message || 'Failed to update subscription');
        }
    }

    async toggleFavorite(button) {
        const airdropId = button.dataset.airdropId;
        const isFavorited = button.dataset.favorited === 'true';

        try {
            const response = await fetch(`/airdrops/${airdropId}/favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                throw new Error('Failed to update favorite');
            }

            const result = await response.json();
            
            // Update button state
            button.dataset.favorited = result.favorited;
            const icon = button.querySelector('svg, .icon');
            if (icon) {
                icon.className = result.favorited 
                    ? icon.className.replace('text-gray-400', 'text-red-500')
                    : icon.className.replace('text-red-500', 'text-gray-400');
            }

            this.showSuccess(result.message);

        } catch (error) {
            this.showError(error.message || 'Failed to update favorite');
        }
    }

    showRatingModal(button) {
        const airdropId = button.dataset.airdropId;
        
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rate this Airdrop</h3>
                <div class="flex space-x-2 mb-4">
                    ${[1, 2, 3, 4, 5].map(rating => `
                        <button data-rating="${rating}" class="rating-star text-2xl text-gray-300 hover:text-yellow-500 transition-colors duration-200">
                            ⭐
                        </button>
                    `).join('')}
                </div>
                <div class="flex space-x-3">
                    <button onclick="this.closest('.fixed').remove()" class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors duration-200">
                        Cancel
                    </button>
                    <button id="submit-rating" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200" disabled>
                        Submit
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        let selectedRating = 0;

        // Handle star selection
        modal.querySelectorAll('.rating-star').forEach(star => {
            star.addEventListener('click', () => {
                selectedRating = parseInt(star.dataset.rating);
                
                // Update star display
                modal.querySelectorAll('.rating-star').forEach((s, index) => {
                    s.className = index < selectedRating 
                        ? 'rating-star text-2xl text-yellow-500 transition-colors duration-200'
                        : 'rating-star text-2xl text-gray-300 hover:text-yellow-500 transition-colors duration-200';
                });

                // Enable submit button
                modal.querySelector('#submit-rating').disabled = false;
            });
        });

        // Handle rating submission
        modal.querySelector('#submit-rating').addEventListener('click', async () => {
            try {
                const response = await fetch(`/airdrops/${airdropId}/rate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ rating: selectedRating })
                });

                if (!response.ok) {
                    throw new Error('Failed to submit rating');
                }

                const result = await response.json();
                modal.remove();
                this.showSuccess(result.message);

                // Update rating display on page
                const ratingElement = document.querySelector('[data-rating-display]');
                if (ratingElement) {
                    ratingElement.textContent = `${result.new_rating} (${result.rating_count} ratings)`;
                }

            } catch (error) {
                this.showError(error.message || 'Failed to submit rating');
            }
        });
    }

    showSuccess(message) {
        // Reuse notification system from WalletManager
        if (window.walletManager) {
            window.walletManager.showSuccess(message);
        }
    }

    showError(message) {
        // Reuse notification system from WalletManager
        if (window.walletManager) {
            window.walletManager.showError(message);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.walletManager = new WalletManager();
    window.airdropInteractions = new AirdropInteractions();
});

// Theme switching functionality
document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.querySelector('[data-theme-toggle]');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    }
});

// Search functionality
class SearchManager {
    constructor() {
        this.searchInput = document.querySelector('[data-search-input]');
        this.searchResults = document.querySelector('[data-search-results]');
        this.searchTimeout = null;
        
        if (this.searchInput) {
            this.init();
        }
    }

    init() {
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch(e.target.value);
            }, 300);
        });

        this.searchInput.addEventListener('focus', () => {
            if (this.searchResults) {
                this.searchResults.classList.remove('hidden');
            }
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('[data-search-container]')) {
                if (this.searchResults) {
                    this.searchResults.classList.add('hidden');
                }
            }
        });
    }

    async performSearch(query) {
        if (query.length < 2) {
            if (this.searchResults) {
                this.searchResults.classList.add('hidden');
            }
            return;
        }

        try {
            const response = await fetch(`/api/v1/airdrops/search?q=${encodeURIComponent(query)}`);
            if (!response.ok) {
                throw new Error('Search failed');
            }

            const results = await response.json();
            this.displayResults(results);

        } catch (error) {
            console.error('Search error:', error);
        }
    }

    displayResults(results) {
        if (!this.searchResults) return;

        if (results.length === 0) {
            this.searchResults.innerHTML = `
                <div class="p-4 text-gray-500 dark:text-gray-400 text-center">
                    No results found
                </div>
            `;
        } else {
            this.searchResults.innerHTML = results.map(airdrop => `
                <a href="/airdrops/${airdrop.slug}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-600 last:border-b-0">
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 dark:text-white">${airdrop.title}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">${airdrop.project.name} • ${airdrop.blockchain.name}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${this.getStatusBadgeClass(airdrop.status)} text-white">
                            ${airdrop.status.charAt(0).toUpperCase() + airdrop.status.slice(1)}
                        </span>
                    </div>
                </a>
            `).join('');
        }

        this.searchResults.classList.remove('hidden');
    }

    getStatusBadgeClass(status) {
        const classes = {
            active: 'bg-green-500',
            upcoming: 'bg-blue-500',
            ended: 'bg-gray-500',
            cancelled: 'bg-red-500'
        };
        return classes[status] || 'bg-gray-400';
    }
}

// Initialize search when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new SearchManager();
});
