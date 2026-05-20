import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, Link, useNavigate, useParams } from 'react-router-dom';
import axios from 'axios';

// API Configuration
const API_URL = 'http://localhost:5000/api';

// Context Providers
const AuthContext = React.createContext();
const CartContext = React.createContext();

// Auth Provider
function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem('token');
    const userData = localStorage.getItem('user');
    if (token && userData) {
      setUser(JSON.parse(userData));
    }
    setLoading(false);
  }, []);

  const login = async (email, password) => {
    try {
      const res = await axios.post(`${API_URL}/auth/login`, { email, password });
      localStorage.setItem('token', res.data.token);
      localStorage.setItem('user', JSON.stringify(res.data.user));
      setUser(res.data.user);
      return { success: true };
    } catch (error) {
      return { success: false, error: error.response?.data?.error || 'Login failed' };
    }
  };

  const register = async (name, email, password) => {
    try {
      const res = await axios.post(`${API_URL}/auth/register`, { name, email, password });
      localStorage.setItem('token', res.data.token);
      localStorage.setItem('user', JSON.stringify(res.data.user));
      setUser(res.data.user);
      return { success: true };
    } catch (error) {
      return { success: false, error: error.response?.data?.error || 'Registration failed' };
    }
  };

  const logout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, login, register, logout, loading }}>
      {children}
    </AuthContext.Provider>
  );
}

// Cart Provider
function CartProvider({ children }) {
  const { user } = React.useContext(AuthContext);
  const [cart, setCart] = useState([]);
  const [cartCount, setCartCount] = useState(0);

  useEffect(() => {
    if (user) {
      const saved = localStorage.getItem(`cart_${user.id}`);
      if (saved) {
        const cartData = JSON.parse(saved);
        setCart(cartData);
        setCartCount(cartData.reduce((sum, i) => sum + i.quantity, 0));
      }
    } else {
      setCart([]);
      setCartCount(0);
    }
  }, [user]);

  useEffect(() => {
    if (user) {
      localStorage.setItem(`cart_${user.id}`, JSON.stringify(cart));
      setCartCount(cart.reduce((sum, i) => sum + i.quantity, 0));
    }
  }, [cart, user]);

  const addToCart = (product, size, color) => {
    if (!user) {
      alert('Please login first');
      return false;
    }
    const existing = cart.find(i => i.productId === product.id && i.size === size && i.color === color);
    if (existing) {
      setCart(cart.map(i => i.id === existing.id ? { ...i, quantity: i.quantity + 1 } : i));
    } else {
      setCart([...cart, { id: Date.now(), productId: product.id, name: product.name, price: product.price, image: product.image, size, color, quantity: 1 }]);
    }
    return true;
  };

  const updateQuantity = (id, quantity) => {
    if (quantity <= 0) {
      setCart(cart.filter(i => i.id !== id));
    } else {
      setCart(cart.map(i => i.id === id ? { ...i, quantity } : i));
    }
  };

  const removeItem = (id) => setCart(cart.filter(i => i.id !== id));
  const getTotal = () => cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
  const clearCart = () => setCart([]);

  return (
    <CartContext.Provider value={{ cart, cartCount, addToCart, updateQuantity, removeItem, getTotal, clearCart }}>
      {children}
    </CartContext.Provider>
  );
}

// Main App Component
function App() {
  return (
    <Router>
      <AuthProvider>
        <CartProvider>
          <div className="min-h-screen bg-gray-50">
            <Navbar />
            <Routes>
              <Route path="/" element={<Home />} />
              <Route path="/products" element={<Products />} />
              <Route path="/product/:id" element={<ProductDetail />} />
              <Route path="/cart" element={<Cart />} />
              <Route path="/checkout" element={<Checkout />} />
              <Route path="/login" element={<Login />} />
              <Route path="/register" element={<Register />} />
              <Route path="/profile" element={<Profile />} />
              <Route path="/admin/*" element={<AdminDashboard />} />
            </Routes>
            <Footer />
          </div>
        </CartProvider>
      </AuthProvider>
    </Router>
  );
}

// Navbar Component
function Navbar() {
  const { user, logout } = React.useContext(AuthContext);
  const { cartCount } = React.useContext(CartContext);
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  return (
    <nav className="bg-white shadow-md sticky top-0 z-50">
      <div className="container mx-auto px-4 py-4 flex justify-between items-center">
        <Link to="/" className="text-2xl font-bold text-primary">
          <i className="fas fa-prescription-bottle-alt mr-2"></i>
          PharmaCare
        </Link>
        
        <div className="hidden md:flex space-x-8">
          <Link to="/" className="text-gray-700 hover:text-primary">Home</Link>
          <Link to="/products" className="text-gray-700 hover:text-primary">Products</Link>
          <Link to="/about" className="text-gray-700 hover:text-primary">About</Link>
          <Link to="/contact" className="text-gray-700 hover:text-primary">Contact</Link>
        </div>

        <div className="flex items-center space-x-4">
          <Link to="/cart" className="relative">
            <i className="fas fa-shopping-cart text-gray-700 text-xl"></i>
            {cartCount > 0 && (
              <span className="absolute -top-2 -right-3 bg-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                {cartCount}
              </span>
            )}
          </Link>
          
          {user ? (
            <div className="relative">
              <button onClick={() => setIsMenuOpen(!isMenuOpen)} className="flex items-center space-x-2">
                <i className="fas fa-user-circle text-gray-700 text-2xl"></i>
                <span>{user.name.split(' ')[0]}</span>
              </button>
              {isMenuOpen && (
                <div className="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border">
                  <Link to="/profile" className="block px-4 py-2 hover:bg-gray-100">Profile</Link>
                  {user.role === 'admin' && <Link to="/admin" className="block px-4 py-2 hover:bg-gray-100">Admin Panel</Link>}
                  <button onClick={logout} className="block w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600">Logout</button>
                </div>
              )}
            </div>
          ) : (
            <div className="space-x-2">
              <Link to="/login" className="btn btn-outline">Login</Link>
              <Link to="/register" className="btn btn-primary">Sign Up</Link>
            </div>
          )}
        </div>
      </div>
    </nav>
  );
}

// Home Component
function Home() {
  const [featuredProducts, setFeaturedProducts] = useState([]);
  const [stats, setStats] = useState({});

  useEffect(() => {
    // Fetch featured products
    const fetchProducts = async () => {
      try {
        const res = await axios.get(`${API_URL}/products?limit=6`);
        setFeaturedProducts(res.data);
      } catch (error) {
        console.error('Error fetching products:', error);
      }
    };
    fetchProducts();
  }, []);

  return (
    <div>
      {/* Hero Section */}
      <section className="hero">
        <div className="container mx-auto px-4 py-20">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <div>
              <div className="hero-badge">
                <i className="fas fa-shield-alt"></i>
                <span>Trusted Since 1995</span>
              </div>
              <h1 className="text-5xl font-bold mt-4 mb-6">
                Your Health,<br />
                <span className="gradient-text">Our Priority</span>
              </h1>
              <p className="text-gray-600 mb-8">
                Get authentic medicines, healthcare products, and expert advice delivered to your doorstep. 
                24/7 online consultation available.
              </p>
              <div className="flex gap-4">
                <Link to="/products" className="btn btn-primary btn-large">Shop Now</Link>
                <a href="#features" className="btn btn-outline btn-large">Learn More</a>
              </div>
              <div className="flex gap-8 mt-8">
                <div><div className="text-2xl font-bold text-primary">50k+</div><div className="text-sm text-gray-500">Happy Customers</div></div>
                <div><div className="text-2xl font-bold text-primary">500+</div><div className="text-sm text-gray-500">Products</div></div>
                <div><div className="text-2xl font-bold text-primary">24/7</div><div className="text-sm text-gray-500">Support</div></div>
              </div>
            </div>
            <div className="relative">
              <img src="https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=600" alt="Pharmacist" className="rounded-2xl shadow-xl" />
              <div className="absolute -top-5 -left-5 bg-white rounded-full px-4 py-2 shadow-lg flex items-center gap-2">
                <i className="fas fa-truck-fast text-primary"></i>
                <span>Free Delivery</span>
              </div>
              <div className="absolute -bottom-5 -right-5 bg-white rounded-full px-4 py-2 shadow-lg flex items-center gap-2">
                <i className="fas fa-certificate text-primary"></i>
                <span>100% Authentic</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section id="features" className="py-20 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold mb-4">Why Choose Us</h2>
            <p className="text-gray-600">We provide the best healthcare services with quality products</p>
          </div>
          <div className="grid md:grid-cols-4 gap-6">
            {[
              { icon: 'flask', title: 'Quality Medicines', desc: 'All products are genuine and sourced from certified manufacturers' },
              { icon: 'user-md', title: 'Expert Pharmacists', desc: 'Certified pharmacists available for consultation' },
              { icon: 'prescription-bottle', title: 'Prescription Support', desc: 'Upload prescription and get medicines delivered' },
              { icon: 'credit-card', title: 'Secure Payment', desc: 'Multiple payment options with secure checkout' }
            ].map((feature, idx) => (
              <div key={idx} className="bg-white p-6 rounded-xl text-center hover:shadow-lg transition">
                <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                  <i className={`fas fa-${feature.icon} text-2xl text-primary`}></i>
                </div>
                <h3 className="font-bold text-lg mb-2">{feature.title}</h3>
                <p className="text-gray-600 text-sm">{feature.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Products */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold mb-4">Featured Products</h2>
            <p className="text-gray-600">Most popular healthcare items</p>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {featuredProducts.map(product => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}

// Product Card Component
function ProductCard({ product }) {
  const { addToCart } = React.useContext(CartContext);
  const navigate = useNavigate();

  return (
    <div className="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
      <img src={product.image} alt={product.name} className="w-full h-48 object-cover" />
      <div className="p-4">
        <h3 className="font-bold text-lg mb-1">{product.name}</h3>
        <p className="text-gray-500 text-sm mb-2">{product.brand}</p>
        <div className="flex items-center justify-between">
          <span className="text-2xl font-bold text-primary">${product.price}</span>
          {product.prescription_required && (
            <span className="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Prescription Required</span>
          )}
        </div>
        <div className="flex gap-2 mt-3">
          <button onClick={() => addToCart(product, 'M', 'Default')} className="flex-1 bg-primary text-white py-2 rounded-lg hover:bg-primary-dark transition">
            Add to Cart
          </button>
          <button onClick={() => navigate(`/product/${product.id}`)} className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            View
          </button>
        </div>
      </div>
    </div>
  );
}

// Footer Component
function Footer() {
  return (
    <footer className="bg-gray-900 text-white py-12 mt-20">
      <div className="container mx-auto px-4">
        <div className="grid md:grid-cols-4 gap-8">
          <div>
            <h3 className="text-xl font-bold mb-4">PharmaCare</h3>
            <p className="text-gray-400">Your trusted partner in healthcare. We provide quality medicines at affordable prices.</p>
          </div>
          <div>
            <h4 className="font-semibold mb-4">Quick Links</h4>
            <ul className="space-y-2 text-gray-400">
              <li><Link to="/" className="hover:text-white">Home</Link></li>
              <li><Link to="/products" className="hover:text-white">Products</Link></li>
              <li><Link to="/about" className="hover:text-white">About Us</Link></li>
              <li><Link to="/contact" className="hover:text-white">Contact</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="font-semibold mb-4">Customer Service</h4>
            <ul className="space-y-2 text-gray-400">
              <li><a href="#" className="hover:text-white">FAQs</a></li>
              <li><a href="#" className="hover:text-white">Shipping Policy</a></li>
              <li><a href="#" className="hover:text-white">Return Policy</a></li>
              <li><a href="#" className="hover:text-white">Privacy Policy</a></li>
            </ul>
          </div>
          <div>
            <h4 className="font-semibold mb-4">Contact Info</h4>
            <ul className="space-y-2 text-gray-400">
              <li><i className="fas fa-phone mr-2"></i> +1 234 567 890</li>
              <li><i className="fas fa-envelope mr-2"></i> info@pharmacare.com</li>
              <li><i className="fas fa-map-marker-alt mr-2"></i> 123 Health St, Medical City</li>
            </ul>
          </div>
        </div>
        <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
          <p>&copy; 2025 PharmaCare. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
}

// Login Component
function Login() {
  const navigate = useNavigate();
  const { login } = React.useContext(AuthContext);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    const result = await login(email, password);
    if (result.success) {
      navigate('/');
    } else {
      setError(result.error);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center px-4 py-20 bg-gray-50">
      <div className="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full">
        <div className="text-center mb-8">
          <i className="fas fa-prescription-bottle-alt text-4xl text-primary"></i>
          <h1 className="text-2xl font-bold mt-4">Welcome Back</h1>
          <p className="text-gray-600">Login to your account</p>
        </div>
        <form onSubmit={handleSubmit}>
          <div className="mb-4">
            <label className="block text-gray-700 mb-2">Email Address</label>
            <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required />
          </div>
          <div className="mb-4">
            <label className="block text-gray-700 mb-2">Password</label>
            <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required />
          </div>
          {error && <div className="text-red-500 text-sm mb-4">{error}</div>}
          <button type="submit" className="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary-dark transition">Login</button>
        </form>
        <p className="text-center text-gray-600 mt-4">
          Don't have an account? <Link to="/register" className="text-primary hover:underline">Sign Up</Link>
        </p>
      </div>
    </div>
  );
}

// Register Component
function Register() {
  const navigate = useNavigate();
  const { register } = React.useContext(AuthContext);
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    const result = await register(name, email, password);
    if (result.success) {
      navigate('/');
    } else {
      setError(result.error);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center px-4 py-20 bg-gray-50">
      <div className="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full">
        <div className="text-center mb-8">
          <i className="fas fa-prescription-bottle-alt text-4xl text-primary"></i>
          <h1 className="text-2xl font-bold mt-4">Create Account</h1>
          <p className="text-gray-600">Join PharmaCare today</p>
        </div>
        <form onSubmit={handleSubmit}>
          <div className="mb-4">
            <label className="block text-gray-700 mb-2">Full Name</label>
            <input type="text" value={name} onChange={(e) => setName(e.target.value)} className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required />
          </div>
          <div className="mb-4">
            <label className="block text-gray-700 mb-2">Email Address</label>
            <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required />
          </div>
          <div className="mb-4">
            <label className="block text-gray-700 mb-2">Password</label>
            <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required />
          </div>
          {error && <div className="text-red-500 text-sm mb-4">{error}</div>}
          <button type="submit" className="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary-dark transition">Register</button>
        </form>
        <p className="text-center text-gray-600 mt-4">
          Already have an account? <Link to="/login" className="text-primary hover:underline">Login</Link>
        </p>
      </div>
    </div>
  );
}

export default App;
