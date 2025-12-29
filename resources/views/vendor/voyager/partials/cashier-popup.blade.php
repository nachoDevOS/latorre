<div class="pro-wallet-container">
    <div class="pro-wallet-fab">
        <i class="fa-solid fa-chart-pie"></i>
    </div>

    <div class="pro-wallet-window">
        <div class="pro-wallet-header">
            <h6>Resumen de Saldo</h6>
            <a href="#" title="Actualizar" onclick="event.preventDefault(); location.reload();"
                class="pro-refresh-button">
                <i class="fa-solid fa-arrows-rotate"></i>
            </a>
        </div>

        @if (!$globalFuntion_cashierMoney['cashier'])
            <div class="pro-wallet-empty">
                <p>No hay ninguna caja abierta.</p>
            </div>
        @else
            <div class="pro-wallet-body">
                <div class="pro-balance-summary">
                    <small>Efectivo Disponible</small>
                    <span class="total-amount">Bs.
                        {{ number_format($globalFuntion_cashierMoney['amountCashier'], 2, ',', '.') }}</span>
                </div>
                <ul class="pro-balance-details">
                    <li>
                        <div class="detail-label">
                            <i class="fa-solid fa-dollar-sign detail-icon income"></i>
                            Ingreso Efectivo
                        </div>
                        <span class="detail-amount income">+ Bs.
                            {{ number_format($globalFuntion_cashierMoney['paymentEfectivo'], 2, ',', '.') }}</span>
                    </li>
                    <li>
                        <div class="detail-label">
                            <i class="fa-solid fa-qrcode detail-icon income"></i>
                            Ingreso Qr
                        </div>
                        <span class="detail-amount income">+ Bs.
                            {{ number_format($globalFuntion_cashierMoney['paymentQr'], 2, ',', '.') }}</span>
                    </li>
                    <li>
                        <div class="detail-label">
                            <i class="fa-solid fa-arrow-down detail-icon expense"></i>
                            Gastos
                        </div>
                        <span class="detail-amount expense">- Bs.
                            {{ number_format($globalFuntion_cashierMoney['cashierOut'], 2, ',', '.') }}</span>
                    </li>
                    <li>
                        <div class="detail-label">
                            <i class="fa-solid fa-arrow-up detail-icon assigned"></i>
                            Asignado a Caja
                        </div>
                        <span class="detail-amount assigned">+ Bs.
                            {{ number_format($globalFuntion_cashierMoney['cashierIn'], 2, ',', '.') }}</span>
                    </li>
                </ul>
            </div>
        @endif
    </div>
</div>

<style>
    /* --- Professional Wallet Widget --- */
    .pro-wallet-container {
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 1200;
        font-family: 'Open Sans', sans-serif;
    }

    .pro-wallet-fab {
        width: 56px;
        height: 56px;
        background-color: #2c3e50;
        /* Dark blue-grey */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 22px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease-in-out;
    }

    .pro-wallet-container:hover .pro-wallet-fab {
        transform: scale(1.05);
        background-color: #34495e;
        /* Slightly lighter */
    }

    .pro-wallet-window {
        position: absolute;
        bottom: 70px;
        right: 0;
        width: 350px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(44, 62, 80, 0.2);
        border: 1px solid #e7eaf3;

        /* Animation */
        opacity: 0;
        visibility: hidden;
        transform: translateY(15px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .pro-wallet-container:hover .pro-wallet-window {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .pro-wallet-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        border-bottom: 1px solid #e7eaf3;
    }

    .pro-wallet-header h6 {
        margin: 0;
        font-weight: 600;
        font-size: 1rem;
        color: #2c3e50;
    }

    .pro-refresh-button {
        color: #95a5a6;
        transition: all 0.2s ease;
    }

    .pro-refresh-button:hover {
        color: #2c3e50;
        transform: rotate(135deg);
    }

    .pro-wallet-empty {
        padding: 40px 20px;
        text-align: center;
        color: #7f8c8d;
    }

    .pro-wallet-body {
        padding: 0;
    }

    .pro-balance-summary {
        padding: 20px;
        background-color: #f8f9fa;
        text-align: center;
    }

    .pro-balance-summary small {
        font-size: 0.85rem;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pro-balance-summary .total-amount {
        display: block;
        font-size: 2.25rem;
        font-weight: 700;
        color: #2c3e50;
        margin-top: 5px;
    }

    .pro-balance-details {
        list-style: none;
        padding: 10px 20px;
        margin: 0;
    }

    .pro-balance-details li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f1f3f5;
    }

    .pro-balance-details li:last-child {
        border-bottom: none;
    }

    .detail-label {
        display: flex;
        align-items: center;
        font-size: 0.95rem;
        color: #34495e;
    }

    .detail-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: white;
    }

    .detail-icon.income {
        background-color: #27ae60;
    }

    /* Green */
    .detail-icon.expense {
        background-color: #c0392b;
    }

    /* Red */
    .detail-icon.assigned {
        background-color: #2980b9;
    }

    /* Blue */

    .detail-amount {
        font-weight: 600;
        font-size: 1rem;
    }

    .detail-amount.income {
        color: #27ae60;
    }

    .detail-amount.expense {
        color: #c0392b;
    }

    .detail-amount.assigned {
        color: #2980b9;
    }
</style>
