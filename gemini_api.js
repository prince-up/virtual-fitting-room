// Gemini API configuration
const GEMINI_API_KEY = 'AIzaSyBA8m9ZxPDLSV6NqYnx48ARntCWfMrE9Sg';
const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent';

// Function to search for saree images using Gemini API
async function searchSareeImages(query) {
    try {
        const response = await fetch(`${GEMINI_API_URL}?key=${GEMINI_API_KEY}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                contents: [{
                    parts: [{
                        text: `Search for high-quality images of ${query} saree. Return only the image URLs.`
                    }]
                }]
            })
        });

        if (!response.ok) {
            throw new Error('Failed to fetch images');
        }

        const data = await response.json();
        return data.candidates[0].content.parts[0].text;
    } catch (error) {
        console.error('Error searching images:', error);
        return null;
    }
}

// Saree product data with search queries
const sareeProducts = [
    {
        id: 31,
        name: "Silk Saree",
        price: 2999,
        query: "traditional silk saree with golden border",
        gender: "female",
        sizes: ["Free Size"],
        colors: ["Red", "Gold"],
        category: "sarees"
    },
    {
        id: 32,
        name: "Banarasi Saree",
        price: 4999,
        query: "banarasi silk saree with intricate zari work",
        gender: "female",
        sizes: ["Free Size"],
        colors: ["Maroon", "Gold"],
        category: "sarees"
    },
    {
        id: 33,
        name: "Cotton Saree",
        price: 1999,
        query: "cotton saree with traditional prints",
        gender: "female",
        sizes: ["Free Size"],
        colors: ["Blue", "White"],
        category: "sarees"
    },
    {
        id: 34,
        name: "Designer Saree",
        price: 3999,
        query: "modern designer saree with contemporary patterns",
        gender: "female",
        sizes: ["Free Size"],
        colors: ["Pink", "Silver"],
        category: "sarees"
    },
    {
        id: 35,
        name: "Wedding Saree",
        price: 5999,
        query: "bridal wedding saree with heavy embroidery",
        gender: "female",
        sizes: ["Free Size"],
        colors: ["Red", "Gold"],
        category: "sarees"
    },
    {
        id: 36,
        name: "Party Wear Saree",
        price: 3499,
        query: "party wear saree with sequin work",
        gender: "female",
        sizes: ["Free Size"],
        colors: ["Black", "Gold"],
        category: "sarees"
    },
    {
        id: 37,
        name: "Traditional Saree",
        price: 2499,
        query: "traditional south indian saree with temple border",
        gender: "female",
        sizes: ["Free Size"],
        colors: ["Green", "Gold"],
        category: "sarees"
    },
    {
        id: 38,
        name: "Printed Saree",
        price: 1799,
        query: "printed cotton saree with floral patterns",
        gender: "female",
        sizes: ["Free Size"],
        colors: ["Yellow", "White"],
        category: "sarees"
    },
    {
        id: 39,
        name: "Georgette Saree",
        price: 2799,
        query: "georgette saree with digital prints",
        gender: "female",
        sizes: ["Free Size"],
        colors: ["Purple", "Silver"],
        category: "sarees"
    },
    {
        id: 40,
        name: "Chiffon Saree",
        price: 2299,
        query: "chiffon saree with stone work",
        gender: "female",
        sizes: ["Free Size"],
        colors: ["Peach", "Gold"],
        category: "sarees"
    }
];

// Function to update saree images using Gemini API
async function updateSareeImages() {
    for (const product of sareeProducts) {
        const imageUrl = await searchSareeImages(product.query);
        if (imageUrl) {
            product.image = imageUrl;
        }
    }
    return sareeProducts;
}

// Export functions for use in other files
window.updateSareeImages = updateSareeImages;
window.sareeProducts = sareeProducts; 