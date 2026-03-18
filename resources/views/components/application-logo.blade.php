<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <!-- Open Book with Encyclopedia Theme -->
    <g transform="translate(100, 100)">
        <!-- Left Page -->
        <path d="M -70 -40 Q -70 -60 -50 -60 L -5 -60 L -5 50 L -50 50 Q -70 50 -70 30 Z" 
              fill="#667eea" opacity="0.9"/>
        
        <!-- Right Page -->
        <path d="M 5 -60 L 50 -60 Q 70 -60 70 -40 L 70 30 Q 70 50 50 50 L 5 50 Z" 
              fill="#764ba2" opacity="0.9"/>
        
        <!-- Page Highlight (Left) -->
        <path d="M -65 -35 Q -65 -55 -50 -55 L -10 -55 L -10 -50 L -50 -50 Q -60 -50 -60 -40 L -60 25 Q -60 35 -50 35 L -10 35 L -10 40 L -50 40 Q -65 40 -65 25 Z" 
              fill="white" opacity="0.3"/>
        
        <!-- Page Highlight (Right) -->
        <path d="M 10 -55 L 50 -55 Q 65 -55 65 -35 L 65 25 Q 65 40 50 40 L 10 40 L 10 35 L 50 35 Q 60 35 60 25 L 60 -40 Q 60 -50 50 -50 L 10 -50 Z" 
              fill="white" opacity="0.3"/>
        
        <!-- Book Spine -->
        <rect x="-5" y="-60" width="10" height="110" fill="#2d3748"/>
        <rect x="-4" y="-58" width="8" height="106" fill="#4a5568"/>
        
        <!-- Text Lines on Left Page -->
        <line x1="-55" y1="-45" x2="-15" y2="-45" stroke="white" stroke-width="2" opacity="0.8"/>
        <line x1="-55" y1="-35" x2="-15" y2="-35" stroke="white" stroke-width="2" opacity="0.8"/>
        <line x1="-55" y1="-25" x2="-15" y2="-25" stroke="white" stroke-width="2" opacity="0.8"/>
        <line x1="-55" y1="-15" x2="-30" y2="-15" stroke="white" stroke-width="2" opacity="0.8"/>
        
        <line x1="-55" y1="0" x2="-15" y2="0" stroke="white" stroke-width="2" opacity="0.8"/>
        <line x1="-55" y1="10" x2="-15" y2="10" stroke="white" stroke-width="2" opacity="0.8"/>
        <line x1="-55" y1="20" x2="-25" y2="20" stroke="white" stroke-width="2" opacity="0.8"/>
        
        <!-- Text Lines on Right Page -->
        <line x1="15" y1="-45" x2="55" y2="-45" stroke="white" stroke-width="2" opacity="0.8"/>
        <line x1="15" y1="-35" x2="55" y2="-35" stroke="white" stroke-width="2" opacity="0.8"/>
        <line x1="15" y1="-25" x2="55" y2="-25" stroke="white" stroke-width="2" opacity="0.8"/>
        <line x1="15" y1="-15" x2="40" y2="-15" stroke="white" stroke-width="2" opacity="0.8"/>
        
        <line x1="15" y1="0" x2="55" y2="0" stroke="white" stroke-width="2" opacity="0.8"/>
        <line x1="15" y1="10" x2="55" y2="10" stroke="white" stroke-width="2" opacity="0.8"/>
        <line x1="15" y1="20" x2="35" y2="20" stroke="white" stroke-width="2" opacity="0.8"/>
        
        <!-- Decorative Pen/Pencil on Right Page -->
        <g transform="translate(35, -50) rotate(25)">
            <rect x="-2" y="0" width="4" height="30" rx="1" fill="#fbbf24"/>
            <polygon points="-2,30 2,30 0,35" fill="#78350f"/>
            <rect x="-2.5" y="-3" width="5" height="3" fill="#94a3b8"/>
        </g>
        
        <!-- Encyclopedia Star Symbol (Top Center) -->
        <g transform="translate(0, -70)">
            <circle cx="0" cy="0" r="15" fill="#48bb78" opacity="0.9"/>
            <path d="M 0 -8 L 2 -2 L 8 -2 L 3 2 L 5 8 L 0 4 L -5 8 L -3 2 L -8 -2 L -2 -2 Z" 
                  fill="white"/>
        </g>
    </g>
</svg>
