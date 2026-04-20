<style>
    * { box-sizing: border-box; }
    .subtitle { color: #64748b; margin-bottom: 28px; font-size: 1.125rem; }
    .calendar-container {
      background: white;
      border-radius: 16px;
      padding: 28px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
      max-width: 960px;
      margin: 0 auto;
      border: 1px solid #e2e8f0;
    }
    .month-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 28px;
      color: #1e293b;
    }
    .month-header h2 { margin: 0; font-size: 1.75rem; font-weight: 600; }
    .btn-nav {
      background: #ecfdf5;
      border: none;
      color: #047857;
      font-size: 1.5rem;
      width: 48px;
      height: 48px;
      border-radius: 50%;
      cursor: pointer;
      transition: all 0.2s;
      border: 1px solid #d1fae5;
    }
    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 10px;
      text-align: center;
    }
    .day-name {
      font-weight: 600;
      color: #64748b;
      padding: 14px 0;
      font-size: 0.95rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .day {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 12px 8px;
      min-height: 110px;
      position: relative;
      font-weight: 500;
      color: #1e293b;
      overflow: hidden;
    }
    .day.empty { background: transparent; border: none; pointer-events: none; }
    .day.event { background: #ecfdf5; border: 2px solid #10b981; font-weight: 600; }
    .day-number { font-size: 1.25rem; font-weight: 600; margin-bottom: 6px; display: block; }
    .event-badge {
      position: absolute;
      bottom: 10px;
      left: 10px;
      right: 10px;
      font-size: 0.82rem;
      padding: 6px 8px;
      border-radius: 6px;
      color: white;
      font-weight: 500;
      text-align: left;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .event-badge.training { background: #3b82f6; }
    .event-badge.match { background: #dc2626; }
</style>

<div id="calendario">
    <h1>Calendario</h1>
    <p class="subtitle">Próximos partidos y entrenamientos del equipo</p>

    <div class="calendar-container">
        <div class="month-header">
            <button class="btn-nav" type="button">&lt;</button>
            <h2>Marzo 2025</h2>
            <button class="btn-nav" type="button">&gt;</button>
        </div>

        <div class="calendar-grid">
            <div class="day-name">Lun</div>
            <div class="day-name">Mar</div>
            <div class="day-name">Mié</div>
            <div class="day-name">Jue</div>
            <div class="day-name">Vie</div>
            <div class="day-name">Sáb</div>
            <div class="day-name">Dom</div>

            <div class="day empty"></div>
            <div class="day empty"></div>
            <div class="day">1</div>
            <div class="day event">
                 <div class="day-number">2</div>
                 <div class="event-badge training">Entrenamiento 16:00</div>
            </div>
            <div class="day">3</div>
            <div class="day event">
                <div class="day-number">4</div>
                <div class="event-badge training">Entrenamiento 18:00</div>
            </div>
            <div class="day">5</div>
            <div class="day event">
                <div class="day-number">6</div>
                <div class="event-badge match">vs River Plate 20:30</div>
            </div>
            <div class="day">8</div>
            <div class="day">9</div>
            <div class="day">10</div>
            <div class="day">11</div>
            <div class="day event">
                <div class="day-number">12</div>
                <div class="event-badge training">Entrenamiento 17:30</div>
            </div>
            <div class="day">13</div>
            <div class="day event">
                <div class="day-number">14</div>
                <div class="event-badge match">vs Boca Juniors 19:00</div>
            </div>
        </div>
    </div>
</div>
