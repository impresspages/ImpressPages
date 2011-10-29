<style>
.ipUploadButtons {
    position: absolute; z-index: 100;
}

.ipUploadWindow{
    border: 1px black solid;
    overflow: hidden;
}

.ipUploadBrowseButton{
    cursor: pointer;
}
</style>

<div class="ipUploadWindow">
    <div class="ipUploadButtons">
        <div class="ipUploadBrowseContainer" id="ipUploadContainer_' + uniqueId + '">
            <div class="ipUploadBrowseButton" id="ipUploadButton_' + uniqueId + '">Upload new</div>
        </div>
        <div class="ipUploadLargerButton">Larger</div>
        <div class="ipUploadSmallerButton">Smaller</div>
    </div>
    <div class="ipUploadDragContainer">
        <img class="ipUploadImage" src="" alt="picture" />
    </div>
</div>
