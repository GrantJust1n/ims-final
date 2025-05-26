import React, { useEffect, useState } from 'react';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js';
import { Bar } from 'react-chartjs-2';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

function Dashboard() {
  const [totalProducts, setTotalProducts] = useState(0);
  const [totalStock, setTotalStock] = useState(0);
  const [lowStockCount, setLowStockCount] = useState(0);

  useEffect(() => {
    async function fetchStats() {
      try {
        const [productsRes, stockRes, lowStockRes] = await Promise.all([
          fetch('http://localhost/ims/backend/index.php?action=total_products'),
          fetch('http://localhost/ims/backend/index.php?action=total_stock'),
          fetch('http://localhost/ims/backend/index.php?action=low_stock_count'),
        ]);

        const productsData = await productsRes.json();
        const stockData = await stockRes.json();
        const lowStockData = await lowStockRes.json();

        if (productsData.type === 'success') setTotalProducts(productsData.total_products);
        if (stockData.type === 'success') setTotalStock(stockData.total_stock);
        if (lowStockData.type === 'success') setLowStockCount(lowStockData.low_stock_count);
      } catch (error) {
        console.error("Failed to fetch stats:", error);
      }
    }

    fetchStats();
  }, []);

  const chartData = {
    labels: ['Total Products', 'Total Stock', 'Low Stock'],
    datasets: [
      {
        label: 'Inventory Metrics',
        data: [totalProducts, totalStock, lowStockCount],
        backgroundColor: [
          'rgba(59, 130, 246, 0.7)',   // blue
          'rgba(34, 197, 94, 0.7)',    // green
          'rgba(239, 68, 68, 0.7)',    // red
        ],
        borderRadius: 6
      }
    ]
  };

  const chartOptions = {
    responsive: true,
    plugins: {
      legend: { position: 'top' },
      title: { display: true, text: 'Inventory Overview' }
    }
  };

  return (
    <div className="flex items-center justify-center min-h-screen bg-gray-100 p-6">
      <div className="w-full max-w-6xl">
        <h1 className="text-3xl font-bold mb-6 text-center">Inventory Management Dashboard</h1>

        {/* Overview Stats */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="bg-white p-6 rounded shadow text-center">
            <h2 className="text-xl font-semibold mb-2">Total Products</h2>
            <p className="text-3xl text-blue-600">{totalProducts}</p>
          </div>
          <div className="bg-white p-6 rounded shadow text-center">
            <h2 className="text-xl font-semibold mb-2">Total Stock</h2>
            <p className="text-3xl text-green-600">{totalStock}</p>
          </div>
          <div className="bg-white p-6 rounded shadow text-center">
            <h2 className="text-xl font-semibold mb-2">Low Stock Items</h2>
            <p className="text-3xl text-red-600">{lowStockCount}</p>
          </div>
        </div>

        {/* Chart Section */}
        <div className="bg-white p-6 rounded shadow">
          <h2 className="text-2xl font-semibold mb-4 text-center">Inventory Overview</h2>
          <Bar data={chartData} options={chartOptions} />
        </div>
      </div>
    </div>
  );
}

export default Dashboard;
