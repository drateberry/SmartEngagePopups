/**
 * Minimalist Chart.js implementation for SmartEngage Popups
 * Only what's needed for line charts is included
 */
(function(global) {
  class Chart {
    constructor(ctx, config) {
      this.ctx = ctx;
      this.config = config;
      this.data = config.data;
      this.options = config.options || {};
      this.render();
    }
    
    render() {
      const ctx = this.ctx;
      const canvas = ctx.canvas;
      const width = canvas.width;
      const height = canvas.height;
      
      // Clear canvas
      ctx.clearRect(0, 0, width, height);
      
      // Set chart area
      const chartArea = {
        left: 50,
        top: 20,
        right: width - 20,
        bottom: height - 40
      };
      
      // Calculate chart dimensions
      const chartWidth = chartArea.right - chartArea.left;
      const chartHeight = chartArea.bottom - chartArea.top;
      
      // Draw axes
      ctx.beginPath();
      ctx.moveTo(chartArea.left, chartArea.top);
      ctx.lineTo(chartArea.left, chartArea.bottom);
      ctx.lineTo(chartArea.right, chartArea.bottom);
      ctx.strokeStyle = '#ddd';
      ctx.stroke();
      
      // Draw labels
      const labels = this.data.labels;
      if (labels && labels.length) {
        const step = chartWidth / (labels.length - 1);
        ctx.fillStyle = '#666';
        ctx.font = '12px Arial';
        ctx.textAlign = 'center';
        
        // Draw only a subset of labels if there are too many
        const labelStep = Math.max(1, Math.floor(labels.length / 10));
        for (let i = 0; i < labels.length; i += labelStep) {
          const x = chartArea.left + (i / (labels.length - 1)) * chartWidth;
          ctx.fillText(labels[i], x, chartArea.bottom + 15);
        }
      }
      
      // Find max value for scaling
      let maxValue = 0;
      this.data.datasets.forEach(dataset => {
        const max = Math.max(...dataset.data);
        if (max > maxValue) maxValue = max;
      });
      
      // Round max value up for better scale
      maxValue = Math.ceil(maxValue / 10) * 10;
      if (maxValue === 0) maxValue = 10; // In case all values are 0
      
      // Draw y-axis labels
      ctx.textAlign = 'right';
      for (let i = 0; i <= 5; i++) {
        const value = (maxValue / 5) * i;
        const y = chartArea.bottom - (i / 5) * chartHeight;
        ctx.fillText(Math.round(value), chartArea.left - 10, y + 4);
        
        // Draw grid line
        ctx.beginPath();
        ctx.moveTo(chartArea.left, y);
        ctx.lineTo(chartArea.right, y);
        ctx.strokeStyle = '#eee';
        ctx.stroke();
      }
      
      // Draw datasets
      this.data.datasets.forEach((dataset, datasetIndex) => {
        ctx.beginPath();
        
        // Create path
        dataset.data.forEach((value, index) => {
          const x = chartArea.left + (index / (dataset.data.length - 1)) * chartWidth;
          const y = chartArea.bottom - (value / maxValue) * chartHeight;
          
          if (index === 0) {
            ctx.moveTo(x, y);
          } else {
            ctx.lineTo(x, y);
          }
        });
        
        // Style & stroke the line
        ctx.strokeStyle = dataset.borderColor;
        ctx.lineWidth = 2;
        ctx.stroke();
        
        // Fill area if backgroundColor is set
        if (dataset.backgroundColor) {
          // Complete the path to the bottom of the chart
          const lastDataPoint = dataset.data.length - 1;
          const lastX = chartArea.left + chartWidth;
          const lastY = chartArea.bottom - (dataset.data[lastDataPoint] / maxValue) * chartHeight;
          
          ctx.lineTo(lastX, chartArea.bottom);
          ctx.lineTo(chartArea.left, chartArea.bottom);
          
          ctx.fillStyle = dataset.backgroundColor;
          ctx.fill();
        }
        
        // Draw points
        dataset.data.forEach((value, index) => {
          const x = chartArea.left + (index / (dataset.data.length - 1)) * chartWidth;
          const y = chartArea.bottom - (value / maxValue) * chartHeight;
          
          ctx.beginPath();
          ctx.arc(x, y, 4, 0, 2 * Math.PI);
          ctx.fillStyle = dataset.borderColor;
          ctx.fill();
          ctx.strokeStyle = '#fff';
          ctx.lineWidth = 1;
          ctx.stroke();
        });
      });
      
      // Draw legend
      if (this.data.datasets.length > 0) {
        const legendX = chartArea.right - 100;
        const legendY = chartArea.top + 10;
        const lineHeight = 25;
        
        this.data.datasets.forEach((dataset, index) => {
          const y = legendY + index * lineHeight;
          
          // Draw color box
          ctx.fillStyle = dataset.borderColor;
          ctx.fillRect(legendX, y, 15, 15);
          
          // Draw label
          ctx.fillStyle = '#666';
          ctx.textAlign = 'left';
          ctx.fillText(dataset.label, legendX + 20, y + 12);
        });
      }
    }
    
    // Placeholder for external API compatibility
    update() {
      this.render();
    }
  }
  
  // Expose Chart to global scope
  global.Chart = Chart;
})(this);
