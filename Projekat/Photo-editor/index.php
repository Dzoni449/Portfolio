<!DOCTYPE html>  
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Editor</title>
    <link rel="stylesheet" href="stil/index.css">
    <style>
        #translateBtn, #autoEditBtn, #applyFilterBtn, #backToPortfolioBtn, #resetBtn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        #translateBtn:hover, #autoEditBtn:hover, #applyFilterBtn:hover, #backToPortfolioBtn:hover, #resetBtn:hover {
            background-color: #45a049;
        }

        #filterInput {
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #creator {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 id="title">Aplikacija za obradu fotografija</h1>
        <div class="upload-container">
            <input type="file" id="uploadImage" accept="image/*">
        </div>
        <div class="canvas-container">
            <canvas id="photoCanvas"></canvas>
        </div>
        <div class="controls">
            <button data-filter="grayscale" class="filterBtn">Siva skala</button>
            <button data-filter="sepia" class="filterBtn">Sepija</button>
            <button data-filter="invert" class="filterBtn">Invertuj boje</button>
            <button data-filter="brightness" data-value="1.2" class="filterBtn">Povećaj osvetljenje</button>
            <button data-filter="contrast" data-value="1.5" class="filterBtn">Povećaj kontrast</button>
            <button data-filter="saturate" data-value="2" class="filterBtn">Povećaj saturaciju</button>
            <button data-filter="blur" data-value="5px" class="filterBtn">Zamagli</button>
        </div>
        <button id="autoEditBtn">Auto-Edit</button>
        <button id="downloadBtn">Preuzmi sliku</button>

        <input type="text" id="filterInput" placeholder="Unesite instrukcije za editovanje slike npr zamagli...">
        <button id="applyFilterBtn">Primeni Edit</button>
        <button id="translateBtn">Prevedi na Engleski</button>
        <button id="resetBtn">Resetuj filtre</button>
        <button id="backToPortfolioBtn">Nazad na portfolio</button>
        
        <div id="creator">Created by: NS</div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('photoCanvas');
            const ctx = canvas.getContext('2d');
            const uploadImage = document.getElementById('uploadImage');
            const downloadBtn = document.getElementById('downloadBtn');
            const translateBtn = document.getElementById('translateBtn');
            const autoEditBtn = document.getElementById('autoEditBtn');
            const backToPortfolioBtn = document.getElementById('backToPortfolioBtn');
            const filterInput = document.getElementById('filterInput');
            const applyFilterBtn = document.getElementById('applyFilterBtn');
            const filterBtns = document.querySelectorAll('.filterBtn');
            const creator = document.getElementById('creator');
            let img = new Image();
            let filters = {};

            uploadImage.addEventListener('change', (e) => {
                const reader = new FileReader();
                reader.onload = (event) => {
                    img.src = event.target.result;
                    img.onload = () => {
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    };
                };
                reader.readAsDataURL(e.target.files[0]);
            });

            filterBtns.forEach(button => {
                button.addEventListener('click', () => {
                    const filter = button.getAttribute('data-filter');
                    const value = button.getAttribute('data-value') || 1;
                    filters[filter] = value;
                    applyFilters();
                });
            });

            function applyFilters() {
                let filterString = Object.keys(filters).map(filter => {
                    return `${filter}(${filters[filter]})`;
                }).join(' ');
                ctx.filter = filterString;
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            }

            downloadBtn.addEventListener('click', () => {
                const link = document.createElement('a');
                link.download = 'edited_image.png';
                link.href = canvas.toDataURL();
                link.click();
            });

            const translations = {
                sr: {
                    title: "Aplikacija za obradu fotografija",
                    grayscale: "Siva skala",
                    sepia: "Sepija",
                    invert: "Invertuj boje",
                    brightness: "Povećaj osvetljenje",
                    contrast: "Povećaj kontrast",
                    saturate: "Povećaj saturaciju",
                    blur: "Zamagli",
                    autoEdit: "Auto-Edit",
                    download: "Preuzmi sliku",
                    applyEdit: "Primeni Edit",
                    translate: "Prevedi na Engleski",
                    backToPortfolio: "Nazad na portfolio",
                    filterPlaceholder: "Unesite instrukcije za editovanje slike npr zamagli..."
                },
                en: {
                    title: "Photo Editing App",
                    grayscale: "Grayscale",
                    sepia: "Sepia",
                    invert: "Invert Colors",
                    brightness: "Increase Brightness",
                    contrast: "Increase Contrast",
                    saturate: "Increase Saturation",
                    blur: "Blur",
                    autoEdit: "Auto-Edit",
                    download: "Download Image",
                    applyEdit: "Apply Edit",
                    translate: "Translate to Serbian",
                    backToPortfolio: "Back to Portfolio",
                    filterPlaceholder: "Enter image editing instructions something like blur..."
                }
            };

            function translatePage(language) {
                document.getElementById('title').textContent = translations[language].title;
                filterBtns[0].textContent = translations[language].grayscale;
                filterBtns[1].textContent = translations[language].sepia;
                filterBtns[2].textContent = translations[language].invert;
                filterBtns[3].textContent = translations[language].brightness;
                filterBtns[4].textContent = translations[language].contrast;
                filterBtns[5].textContent = translations[language].saturate;
                filterBtns[6].textContent = translations[language].blur;
                autoEditBtn.textContent = translations[language].autoEdit;
                downloadBtn.textContent = translations[language].download;
                applyFilterBtn.textContent = translations[language].applyEdit;
                translateBtn.textContent = translations[language].translate;
                backToPortfolioBtn.textContent = translations[language].backToPortfolio;
                filterInput.placeholder = translations[language].filterPlaceholder;
            }

            translateBtn.addEventListener('click', () => {
                const currentLang = document.documentElement.lang;
                if (currentLang === 'sr') {
                    document.documentElement.lang = 'en';
                    translatePage('en');
                } else {
                    document.documentElement.lang = 'sr';
                    translatePage('sr');
                }
            });

            autoEditBtn.addEventListener('click', () => {
                filters = {
                    brightness: '1.3',
                    contrast: '1.4',
                    saturate: '1.5'
                };
                applyFilters();
            });

            applyFilterBtn.addEventListener('click', () => {
                const userInput = filterInput.value.toLowerCase();
                const language = document.documentElement.lang;

                const actions = {
                    sr: {
                        "povećaj osvetljenje": () => { filters['brightness'] = '1.2'; },
                        "više svetla": () => { filters['brightness'] = '1.2'; },
                        "svetlije": () => { filters['brightness'] = '1.2'; },
                        "smanji osvetljenje": () => { filters['brightness'] = '0.8'; },
                        "dodaj sepiju": () => { filters['sepia'] = '1'; },
                        "sepia": () => { filters['sepia'] = '1'; },
                        "dodaj sivu skalu": () => { filters['grayscale'] = '1'; },
                        "crno-belo": () => { filters['grayscale'] = '1'; },
                        "invertuj boje": () => { filters['invert'] = '1'; },
                        "zamagli": () => { filters['blur'] = '5px'; },
                        "više boje": () => { filters['saturate'] = '2'; },
                        "oštri": () => { filters['contrast'] = '2'; },
                        "više kontrasta": () => { filters['contrast'] = '2'; },
                        "dodaj kontrast": () => { filters['contrast'] = '2'; },
                        "smanji saturaciju": () => { filters['saturate'] = '0.5'; },
                        "crvenije": () => { filters['hue-rotate'] = '15deg'; },
                        "rotiraj boje": () => { filters['hue-rotate'] = '90deg'; }
                    },
                    en: {
                        "increase brightness": () => { filters['brightness'] = '1.2'; },
                        "more light": () => { filters['brightness'] = '1.2'; },
                        "brighter": () => { filters['brightness'] = '1.2'; },
                        "decrease brightness": () => { filters['brightness'] = '0.8'; },
                        "add sepia": () => { filters['sepia'] = '1'; },
                        "sepia": () => { filters['sepia'] = '1'; },
                        "add grayscale": () => { filters['grayscale'] = '1'; },
                        "black and white": () => { filters['grayscale'] = '1'; },
                        "invert colors": () => { filters['invert'] = '1'; },
                        "blur": () => { filters['blur'] = '5px'; },
                        "more color": () => { filters['saturate'] = '2'; },
                        "sharper": () => { filters['contrast'] = '2'; },
                        "increase contrast": () => { filters['contrast'] = '2'; },
                        "decrease saturation": () => { filters['saturate'] = '0.5'; },
                        "more red": () => { filters['hue-rotate'] = '15deg'; },
                        "rotate colors": () => { filters['hue-rotate'] = '90deg'; }
                    }
                };

                const synonyms = {
                    sr: {
                        "osvetljenje": ["više svetla", "svetlije", "povećaj osvetljenje", "svetlo", "osvetli"],
                        "sepia": ["sepija", "dodaj sepiju"],
                        "siva skala": ["crno-belo", "dodaj sivu skalu"],
                        "kontrast": ["više kontrasta", "kontrastnije", "oštri"],
                        "boje": ["više boje", "rotiraj boje", "crvenije"]
                    },
                    en: {
                        "brightness": ["increase brightness", "more light", "brighter"],
                        "sepia": ["add sepia"],
                        "grayscale": ["black and white", "add grayscale"],
                        "contrast": ["increase contrast", "sharper"],
                        "saturation": ["decrease saturation", "more color"],
                        "hue": ["more red", "rotate colors"]
                    }
                };

                const actionSet = actions[language];
                const synonymSet = synonyms[language];

                let found = false;

                for (const key in synonymSet) {
                    synonymSet[key].forEach(synonym => {
                        if (userInput.includes(synonym)) {
                            actionSet[key]();
                            found = true;
                        }
                    });
                }

                if (!found) {
                    for (const key in actionSet) {
                        if (userInput.includes(key)) {
                            actionSet[key]();
                        }
                    }
                }

                applyFilters();
            });

            
            document.getElementById('resetBtn').addEventListener('click', () => {
                filters = {};  
                ctx.filter = 'none';  
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);  
            });

            creator.textContent = "Created by: NS";
        });

        backToPortfolioBtn.addEventListener('click', () => {
                window.location.href = '../index.php'; 
            });
        

    </script>
</body>
</html>
