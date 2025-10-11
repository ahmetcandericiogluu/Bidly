import React, { useState } from 'react';
import './index.css';

type PageType = 'login' | 'register' | 'dashboard';
type UserType = 'customer' | 'vendor';

interface User {
  id: number;
  name: string;
  email: string;
  created_at: string;
}

function App() {
  const [currentPage, setCurrentPage] = useState<PageType>('login');
  const [userType, setUserType] = useState<UserType>('customer');
  const [user, setUser] = useState<User | null>(null);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
  });
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState('');

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setMessage('');

    try {
      const endpoint = userType === 'customer' 
        ? 'http://localhost:8080/api/customer/login'
        : 'http://localhost:8080/api/vendor/login';

      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          email: formData.email,
          password: formData.password,
        }),
      });

      const data = await response.json();
      
      if (data.success) {
        const userData = userType === 'customer' ? data.user : data.vendor;
        setUser(userData);
        setCurrentPage('dashboard');
        setFormData({ name: '', email: '', password: '' });
        setMessage('');
      } else {
        setMessage(`❌ ${data.error}`);
      }
    } catch (error) {
      setMessage(`❌ Bağlantı hatası: ${error instanceof Error ? error.message : 'Bilinmeyen hata'}`);
    } finally {
      setLoading(false);
    }
  };

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setMessage('');

    try {
      const endpoint = userType === 'customer' 
        ? 'http://localhost:8080/api/customer/register'
        : 'http://localhost:8080/api/vendor/register';

      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });

      const data = await response.json();
      
      if (data.success) {
        setMessage(`✅ Kayıt başarılı! Şimdi giriş yapabilirsiniz.`);
        setFormData({ name: '', email: '', password: '' });
        // 2 saniye sonra login sayfasına geç
        setTimeout(() => {
          setCurrentPage('login');
          setMessage('');
        }, 2000);
      } else {
        setMessage(`❌ Hata: ${data.error || data.errors?.join(', ')}`);
      }
    } catch (error) {
      setMessage(`❌ Bağlantı hatası: ${error instanceof Error ? error.message : 'Bilinmeyen hata'}`);
    } finally {
      setLoading(false);
    }
  };

  const switchPage = (page: PageType) => {
    setCurrentPage(page);
    setMessage('');
    setFormData({ name: '', email: '', password: '' });
  };

  const handleLogout = () => {
    setUser(null);
    setCurrentPage('login');
    setMessage('');
    setFormData({ name: '', email: '', password: '' });
  };

  // Dashboard Component
  const Dashboard = () => {
    if (userType === 'vendor') {
      return <VendorDashboard />;
    } else {
      // Customer için basit mesaj - arkadaşın üzerinde çalışıyor
      return (
        <div className="min-h-screen bg-gray-50 flex items-center justify-center">
          <div className="text-center">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">
              Müşteri Paneli
            </h2>
            <p className="text-gray-600">
              Müşteri dashboard'ı geliştirme aşamasında...
            </p>
            <button
              onClick={handleLogout}
              className="mt-4 btn-primary"
            >
              Çıkış Yap
            </button>
          </div>
        </div>
      );
    }
  };

  // Vendor Dashboard Component
  const VendorDashboard = () => (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
          <div className="flex items-center space-x-4">
            <div className="h-8 w-8 bg-green-600 rounded-full flex items-center justify-center">
              <span className="text-white font-bold text-sm">🏪</span>
            </div>
            <h1 className="text-xl font-bold text-gray-900">Bidly Satıcı Paneli</h1>
          </div>
          <div className="flex items-center space-x-4">
            <span className="text-sm text-gray-700">
              Hoş geldiniz, {user?.name} (Satıcı)
            </span>
            <button
              onClick={handleLogout}
              className="text-sm text-blue-600 hover:text-blue-500 transition-colors"
            >
              Çıkış Yap
            </button>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 py-8">
        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div className="bg-white rounded-md border border-gray-200 p-6">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                  <span className="text-white text-sm">📦</span>
                </div>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Toplam Ürün</p>
                <p className="text-2xl font-bold text-gray-900">12</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-md border border-gray-200 p-6">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                  <span className="text-white text-sm">🔨</span>
                </div>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Aktif Açık Artırma</p>
                <p className="text-2xl font-bold text-gray-900">5</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-md border border-gray-200 p-6">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                  <span className="text-white text-sm">💰</span>
                </div>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Bu Ay Satış</p>
                <p className="text-2xl font-bold text-gray-900">₺15,240</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-md border border-gray-200 p-6">
            <div className="flex items-center">
              <div className="flex-shrink-0">
                <div className="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                  <span className="text-white text-sm">👥</span>
                </div>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Toplam Teklif</p>
                <p className="text-2xl font-bold text-gray-900">89</p>
              </div>
            </div>
          </div>
        </div>

        {/* Actions */}
        <div className="mb-8">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-2xl font-bold text-gray-900">Ürünlerim</h2>
            <button className="btn-primary">
              + Yeni Ürün Ekle
            </button>
          </div>
        </div>

        {/* Products Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {/* Sample Product Cards */}
          {[1, 2, 3, 4, 5, 6].map((id) => (
            <div key={id} className="bg-white rounded-md border border-gray-200 p-6">
              <div className="bg-gray-200 h-48 rounded-md mb-4 flex items-center justify-center">
                <span className="text-gray-500">Ürün Görseli</span>
              </div>
              <h3 className="font-medium text-gray-900 mb-2">Satılık Ürün {id}</h3>
              <p className="text-sm text-gray-600 mb-4">Bu benim sattığım ürün açıklaması.</p>
              
              <div className="flex justify-between items-center mb-4">
                <div>
                  <p className="text-sm text-gray-600">Başlangıç Fiyatı</p>
                  <p className="font-bold text-lg text-green-600">₺{(id * 150).toLocaleString()}</p>
                </div>
                <div className="text-right">
                  <p className="text-sm text-gray-600">Durum</p>
                  <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                    id % 3 === 0 ? 'bg-green-100 text-green-800' : 
                    id % 2 === 0 ? 'bg-yellow-100 text-yellow-800' : 
                    'bg-gray-100 text-gray-800'
                  }`}>
                    {id % 3 === 0 ? 'Aktif' : id % 2 === 0 ? 'Beklemede' : 'Taslak'}
                  </span>
                </div>
              </div>

              <div className="flex space-x-2">
                <button className="flex-1 text-sm bg-blue-600 text-white py-2 px-3 rounded-md hover:bg-blue-700 transition-colors">
                  Düzenle
                </button>
                <button className="flex-1 text-sm bg-gray-600 text-white py-2 px-3 rounded-md hover:bg-gray-700 transition-colors">
                  Detay
                </button>
              </div>
            </div>
          ))}
        </div>
      </main>
    </div>
  );

  // Render based on current page
  if (currentPage === 'dashboard' && user) {
    return <Dashboard />;
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
      <div className="max-w-md w-full space-y-8">
        <div>
          <div className="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
            <svg className="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              {currentPage === 'login' ? (
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              ) : (
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              )}
            </svg>
          </div>
          <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">
            {currentPage === 'login' ? 'Bidly\'e Giriş Yap' : 'Bidly\'e Kayıt Ol'}
          </h2>
          <p className="mt-2 text-center text-sm text-gray-600">
            {currentPage === 'login' 
              ? 'Açık artırmalara katılmaya devam et' 
              : 'Açık artırmalara katılmaya başla'
            }
          </p>

          {/* User Type Selector */}
          <div className="mt-4 flex justify-center">
            <div className="flex bg-gray-100 rounded-md p-1">
              <button
                type="button"
                onClick={() => setUserType('customer')}
                className={`px-4 py-2 text-sm font-medium rounded-md transition-colors ${
                  userType === 'customer' 
                    ? 'bg-blue-600 text-white' 
                    : 'text-gray-700 hover:text-blue-600'
                }`}
              >
                👤 Müşteri
              </button>
              <button
                type="button"
                onClick={() => setUserType('vendor')}
                className={`px-4 py-2 text-sm font-medium rounded-md transition-colors ${
                  userType === 'vendor' 
                    ? 'bg-blue-600 text-white' 
                    : 'text-gray-700 hover:text-blue-600'
                }`}
              >
                🏪 Satıcı
              </button>
            </div>
          </div>
        </div>
        
        <form className="mt-8 space-y-6" onSubmit={currentPage === 'login' ? handleLogin : handleRegister}>
          <div className="space-y-4">
            {currentPage === 'register' && (
              <div>
                <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                  {userType === 'customer' ? 'Ad Soyad' : 'Şirket Adı'}
                </label>
                <input
                  id="name"
                  name="name"
                  type="text"
                  required
                  value={formData.name}
                  onChange={handleInputChange}
                  className="input-field mt-1"
                  placeholder={userType === 'customer' ? 'Adınızı ve soyadınızı girin' : 'Şirket adınızı girin'}
                />
              </div>
            )}

            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-700">
                E-posta Adresi
              </label>
              <input
                id="email"
                name="email"
                type="email"
                required
                value={formData.email}
                onChange={handleInputChange}
                className="input-field mt-1"
                placeholder="E-posta adresinizi girin"
              />
            </div>

            <div>
              <label htmlFor="password" className="block text-sm font-medium text-gray-700">
                Şifre
              </label>
              <input
                id="password"
                name="password"
                type="password"
                required
                value={formData.password}
                onChange={handleInputChange}
                className="input-field mt-1"
                placeholder={currentPage === 'login' ? 'Şifrenizi girin' : 'Güçlü bir şifre girin (min 8 karakter)'}
              />
            </div>
          </div>

          {message && (
            <div className={`p-3 rounded-md ${message.includes('✅') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
              {message}
            </div>
          )}

          <div>
            <button
              type="submit"
              disabled={loading}
              className="btn-primary w-full flex items-center justify-center"
            >
              {loading ? (
                currentPage === 'login' ? 'Giriş yapılıyor...' : 'Kaydediliyor...'
              ) : (
                currentPage === 'login' ? 'Giriş Yap' : 'Kayıt Ol'
              )}
            </button>
          </div>

          <div className="text-center">
            <span className="text-sm text-gray-600">
              {currentPage === 'login' ? (
                <>
                  Henüz hesabınız yok mu?{' '}
                  <button
                    type="button"
                    onClick={() => switchPage('register')}
                    className="font-medium text-blue-600 hover:text-blue-500 transition-colors"
                  >
                    Kayıt olun
                  </button>
                </>
              ) : (
                <>
                  Zaten hesabınız var mı?{' '}
                  <button
                    type="button"
                    onClick={() => switchPage('login')}
                    className="font-medium text-blue-600 hover:text-blue-500 transition-colors"
                  >
                    Giriş yapın
                  </button>
                </>
              )}
            </span>
          </div>
        </form>
      </div>
    </div>
  );
}

export default App;