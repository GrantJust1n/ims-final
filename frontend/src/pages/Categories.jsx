import React, { useEffect, useState } from 'react';

function CategoryProducts() {
  const [categories, setCategories] = useState([]);
  const [selectedCategoryId, setSelectedCategoryId] = useState(null);
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  // Fetch categories on load
  useEffect(() => {
    fetch('http://localhost/ims/backend/index.php?action=fetch_categories')
      .then((res) => res.json())
      .then((data) => setCategories(data))
      .catch((err) => {
        console.error('Category fetch error:', err);
        setError('Failed to load categories.');
      });
  }, []);

  // Fetch products when category is selected
  const fetchProductsByCategory = async (categoryId) => {
    setSelectedCategoryId(categoryId);
    setLoading(true);
    setError(null);
    try {
      const res = await fetch(`http://localhost/ims/backend/index.php?action=fetch_products_by_category&category_id=${categoryId}`);
      if (!res.ok) throw new Error('Failed to fetch products');
      const data = await res.json();
      setProducts(data);
    } catch (err) {
      console.error('Product fetch error:', err);
      setError('Failed to load products.');
      setProducts([]);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="p-6">
      <h2 className="text-2xl font-bold mb-6">Select a Category</h2>

      <div className="flex flex-wrap gap-4 mb-8">
        {categories.map((category) => (
          <button
            key={category.id}
            onClick={() => fetchProductsByCategory(category.id)}
            className={`px-4 py-2 rounded-lg border transition ${
              selectedCategoryId === category.id
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-800 hover:bg-blue-100'
            }`}
          >
            {category.name}
          </button>
        ))}
      </div>

      {selectedCategoryId && (
        <>
          <h3 className="text-xl font-semibold mb-6">Products</h3>

          {loading && <p className="text-gray-500 mb-4">Loading products...</p>}
          {error && <p className="text-red-500 mb-4">{error}</p>}

          {!loading && products.length === 0 && !error && (
            <p className="text-gray-600">No products found in this category.</p>
          )}

          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            {products.map((product) => (
              <div
                key={product.pid}
                className="border rounded-xl p-4 shadow hover:shadow-md transition duration-300 ease-in-out bg-white"
              >
                <div className="w-full h-60 flex justify-center items-center bg-gray-50 rounded overflow-hidden mb-3">
                  <img
                    src={`http://localhost/ims/backend/uploads/${product.image_path}`}
                    alt={product.pname}
                    className="max-h-full max-w-full object-contain"
                    onError={(e) => {
                      e.target.onerror = null;
                      e.target.src = "https://via.placeholder.com/150";
                    }}
                  />
                </div>
                <p className="text-center font-medium text-gray-800">{product.pname}</p>
              </div>
            ))}
          </div>
        </>
      )}
    </div>
  );
}

export default CategoryProducts;
