import React from 'react';
import { Link } from 'react-router-dom';
import {
  FaTachometerAlt, FaBox, FaList, FaWarehouse, FaTruck,
  FaExclamationTriangle, FaClipboardList, FaCamera, FaUsers, FaCogs
} from 'react-icons/fa';

const menuItems = [
  { name: 'Dashboard', icon: <FaTachometerAlt />, path: '/dashboard' },
  { name: 'Inventory Management', icon: <FaBox />, path: '/inventory' },
  { name: 'Product Categories', icon: <FaList />, path: '/categories' },
  { name: 'Warehouses', icon: <FaWarehouse />, path: '/warehouses' },
  { name: 'Suppliers', icon: <FaTruck />, path: '/suppliers' },
  { name: 'Low Stock Alerts', icon: <FaExclamationTriangle />, path: '/low-stock' },
  { name: 'Reports', icon: <FaClipboardList />, path: '/reports' },
  { name: 'Barcode & QR Scanner', icon: <FaCamera />, path: '/scanner' },
  { name: 'Users & Roles', icon: <FaUsers />, path: '/users' },
  { name: 'Settings', icon: <FaCogs />, path: '/settings' },
];

const Navbar = () => {
  return (
    <div className="h-screen w-60 bg-gray-900 text-white flex flex-col p-4 shadow-md">
      <h2 className="text-xl font-bold mb-6">Inventory Management</h2>
      <ul className="space-y-2">
        {menuItems.map((item, index) => (
          <li key={index}>
            <Link
              to={item.path}
              className="flex items-center gap-3 px-4 py-2 rounded-md hover:bg-gray-700 transition"
            >
              <span className="text-lg">{item.icon}</span>
              <span className="text-sm">{item.name}</span>
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
};

export default Navbar;
