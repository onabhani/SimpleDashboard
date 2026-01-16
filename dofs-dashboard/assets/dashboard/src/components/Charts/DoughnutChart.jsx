import React, { useRef, useEffect } from 'react';

/**
 * Doughnut chart component using Canvas
 */
export default function DoughnutChart({
    data = [],
    labels = [],
    colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
    size = 160,
    showLegend = true,
    centerText = null,
}) {
    const canvasRef = useRef(null);

    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas || data.length === 0) return;

        const ctx = canvas.getContext('2d');
        const dpr = window.devicePixelRatio || 1;

        canvas.width = size * dpr;
        canvas.height = size * dpr;
        ctx.scale(dpr, dpr);

        ctx.clearRect(0, 0, size, size);

        const total = data.reduce((a, b) => a + b, 0);
        const centerX = size / 2;
        const centerY = size / 2;
        const radius = size / 2 - 10;
        const innerRadius = radius * 0.6;

        let startAngle = -Math.PI / 2;

        data.forEach((value, index) => {
            const sliceAngle = (value / total) * Math.PI * 2;
            const endAngle = startAngle + sliceAngle;

            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, startAngle, endAngle);
            ctx.arc(centerX, centerY, innerRadius, endAngle, startAngle, true);
            ctx.closePath();
            ctx.fillStyle = colors[index % colors.length];
            ctx.fill();

            startAngle = endAngle;
        });

        // Center text
        if (centerText) {
            ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#1f2937';
            ctx.font = 'bold 20px Inter, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(centerText.value, centerX, centerY - 8);

            ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280';
            ctx.font = '11px Inter, sans-serif';
            ctx.fillText(centerText.label, centerX, centerY + 12);
        }

    }, [data, colors, size, centerText]);

    return (
        <div className="flex flex-col items-center gap-4">
            <canvas
                ref={canvasRef}
                style={{ width: `${size}px`, height: `${size}px` }}
                className="block"
            />

            {showLegend && labels.length > 0 && (
                <div className="flex flex-wrap justify-center gap-x-4 gap-y-2">
                    {labels.map((label, index) => (
                        <div key={label} className="flex items-center gap-1.5">
                            <div
                                className="w-2.5 h-2.5 rounded-full"
                                style={{ backgroundColor: colors[index % colors.length] }}
                            />
                            <span className="text-xs text-gray-600 dark:text-gray-400">
                                {label}
                            </span>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
