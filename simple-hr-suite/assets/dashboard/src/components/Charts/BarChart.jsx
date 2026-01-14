import React, { useRef, useEffect } from 'react';

/**
 * Bar chart component using Canvas
 */
export default function BarChart({
    data = [],
    labels = [],
    color = '#3b82f6',
    height = 200,
    showLabels = true,
    showValues = true,
}) {
    const canvasRef = useRef(null);

    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas || data.length === 0) return;

        const ctx = canvas.getContext('2d');
        const dpr = window.devicePixelRatio || 1;

        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        ctx.scale(dpr, dpr);

        const width = rect.width;
        const chartHeight = rect.height;
        const padding = { top: 20, right: 20, bottom: showLabels ? 30 : 10, left: 40 };
        const chartWidth = width - padding.left - padding.right;
        const innerHeight = chartHeight - padding.top - padding.bottom;

        ctx.clearRect(0, 0, width, chartHeight);

        const max = Math.max(...data);
        const barWidth = (chartWidth / data.length) * 0.6;
        const gap = (chartWidth / data.length) * 0.4;

        // Draw bars
        data.forEach((value, index) => {
            const barHeight = (value / max) * innerHeight;
            const x = padding.left + index * (barWidth + gap) + gap / 2;
            const y = padding.top + innerHeight - barHeight;

            // Gradient for bar
            const gradient = ctx.createLinearGradient(x, y, x, padding.top + innerHeight);
            gradient.addColorStop(0, color);
            gradient.addColorStop(1, color + '80');

            ctx.beginPath();
            ctx.roundRect(x, y, barWidth, barHeight, 4);
            ctx.fillStyle = gradient;
            ctx.fill();

            // Value on top
            if (showValues) {
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280';
                ctx.font = '10px Inter, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(formatValue(value), x + barWidth / 2, y - 5);
            }

            // Label at bottom
            if (showLabels && labels[index]) {
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280';
                ctx.font = '10px Inter, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(labels[index], x + barWidth / 2, chartHeight - 8);
            }
        });

        // Draw Y-axis labels
        ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#6b7280' : '#9ca3af';
        ctx.font = '10px Inter, sans-serif';
        ctx.textAlign = 'right';
        ctx.fillText(formatValue(max), padding.left - 5, padding.top + 5);
        ctx.fillText(formatValue(max / 2), padding.left - 5, padding.top + innerHeight / 2);
        ctx.fillText('0', padding.left - 5, padding.top + innerHeight);

    }, [data, labels, color, showLabels, showValues]);

    return (
        <canvas
            ref={canvasRef}
            style={{ width: '100%', height: `${height}px` }}
            className="block"
        />
    );
}

function formatValue(value) {
    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
    if (value >= 1000) return (value / 1000).toFixed(1) + 'K';
    return value.toString();
}
