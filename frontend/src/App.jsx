import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Navbar from './Components/Navbar';
import Dashboard from './pages/Dashboard';
import Inventory from './pages/Inventory';
import Categories from './pages/Categories';
import Warehouses from './pages/Warehouses';
import Suppliers from './pages/Suppliers';
import LowStock from './pages/LowStock';
import Scanner from './pages/Scanner';
import Users from './pages/Users';
import Settings from './pages/Settings';

const App = () => {
  return (
    <Router>
      <div className="min-h-screen flex">
        {/* Fixed Sidebar */}
        <div className="fixed top-0 left-0 p-0 h-screen mr w-64 bg-gray-900 text-white flex flex-col p-4 shadow-md">
          <Navbar />
        </div>
        {}
        <div className="ml-64 p-6 overflow-auto min-h-screen">
          <Routes>
            <Route path="/dashboard" element={<Dashboard />} />
            <Route path="/inventory" element={<Inventory />} />
            <Route path="/categories" element={<Categories />} />
            <Route path="/warehouses" element={<Warehouses />} />
            <Route path="/suppliers" element={<Suppliers />} />
            <Route path="/low-stock" element={<LowStock />} />
            <Route path="/scanner" element={<Scanner />} />
            <Route path="/users" element={<Users />} />
            <Route path="/settings" element={<Settings />} />
            <Route path="/" element={<Dashboard />} />
          </Routes>
        </div>
      </div>
    </Router>
  );
};

export default App;
