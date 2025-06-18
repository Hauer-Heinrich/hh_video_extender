// NodeList forEach Implementation
if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}

window.addEventListener("load", () => {
    const pageLang = document.documentElement.lang.toLowerCase() || 'en';
    const fallbackLang = 'en';
    const videos = document.querySelectorAll("video");

    videos.forEach(video => {
        const tracks = Array.from(video.textTracks);

        // Suche passenden Kapitel-Track basierend auf Sprache
        let selectedTrack = tracks.find(t => t.kind === 'chapters' && t.language === pageLang);

        // Fallback auf Englisch, falls keine passende Sprache gefunden
        if (!selectedTrack) {
            selectedTrack = tracks.find(t => t.kind === 'chapters' && t.language === fallbackLang);
        }

        // Keine passenden Kapitel vorhanden
        if (!selectedTrack) return;

        selectedTrack.mode = "hidden";

        // Kapitel-Container erstellen
        const chapterContainer = document.createElement("div");
        chapterContainer.classList.add("video-chapters");
        video.insertAdjacentElement("afterend", chapterContainer);

        const chapterButtons = [];

        function renderChapters(cues) {
            chapterContainer.innerHTML = '';
            chapterButtons.length = 0;

            for (const cue of cues) {
                const button = document.createElement('button');
                button.className = 'chapter-button';
                button.textContent = cue.text;
                button.addEventListener('click', () => {
                    video.currentTime = cue.startTime;
                    video.play();
                });
                chapterButtons.push({ cue, button });
                chapterContainer.appendChild(button);
            }

            video.addEventListener("timeupdate", () => {
                const currentTime = video.currentTime;
                for (const { cue, button } of chapterButtons) {
                    if (currentTime >= cue.startTime && currentTime < cue.endTime) {
                        button.classList.add('active');
                    } else {
                        button.classList.remove('active');
                    }
                }
            });
        }

        // Polling bis cues geladen sind
        const waitForCues = setInterval(() => {
            if (selectedTrack.cues && selectedTrack.cues.length > 0) {
                clearInterval(waitForCues);
                renderChapters(selectedTrack.cues);
            }
        }, 100);
    });
});
